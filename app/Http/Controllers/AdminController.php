<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\LogAktivitasHelper;
use App\Models\SuratMasuk;
use App\Models\ArsipSurat;
use App\Models\Bidang;
use App\Models\Disposisi;
use App\Models\SuratKeluar;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Services\FileEncryptionService;


class AdminController extends Controller
{

    private $fileEncryptionService;

    public function __construct(FileEncryptionService $fileEncryptionService)
    {
        $this->fileEncryptionService = $fileEncryptionService;
    }

    public function dashboardAdmin()
    {
        LogAktivitasHelper::log('Akses Dashboard', 'Admin mengakses dashboard');

        $adminData = Auth::user();
        $totalSuratMasuk = SuratMasuk::count();
        $totalSuratKeluar = SuratKeluar::count();
        $totalUsers = User::whereIn('role', ['kadis', 'kabid', 'sekretaris', 'pegawai'])->count();

        return response()->view('admin.dashboard', compact('adminData', 'totalSuratMasuk', 'totalSuratKeluar', 'totalUsers'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function logout(Request $request)
    {
        LogAktivitasHelper::log('Logout', 'Admin melakukan logout');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/auth/login');
    }

    public function suratMasuk()
    {
        LogAktivitasHelper::log('Akses Surat Masuk', 'Admin membuka halaman surat masuk');

        $kadis = User::where('role', 'kadis')->first();
        $sekretaris = User::where('role', 'sekretaris')->first();
        $kabid = User::where('role', 'kabid')->first();
        $surats = SuratMasuk::all();
        $adminData = Auth::user();
        $rolesWithBidang = User::with('bidang')
            ->whereIn('role', ['kabid', 'sekretaris', 'kadis'])
            ->get();
        return view('admin.suratMasuk', compact('surats', 'adminData', 'kadis', 'sekretaris', 'kabid', 'rolesWithBidang'));
    }

    public function updateRole(Request $request, $id)
    {
        $surat = SuratMasuk::findOrFail($id);
        $roleIds = $request->input('id_role', []);

        $validRoleIds = User::whereIn('id', $roleIds)->pluck('id')->toArray();

        if (empty($validRoleIds)) {
            return back()->with('error', 'Role tidak valid');
        }

        $surat->roles()->sync($validRoleIds);

        $roleUsers = User::whereIn('id', $validRoleIds)->get();

        foreach ($roleUsers as $user) {
            $urlLogin = $user->generateLoginToken(); // method ini ada di model User
            $pesan = "Halo {$user->name},\nAnda menerima surat masuk baru dengan nomor: {$surat->nomor_surat}\n\nKlik untuk login otomatis (berlaku 15 menit):\n{$urlLogin}";

            Http::withHeaders([
                'Authorization' => 'xyrtzMQSmQZuhN8Tkha9gAEBm9rWDEUsmps4n', // ganti dengan token Fonnte kamu
            ])->asForm()->post('https://api.fonnte.com/send', [
                        'target' => $user->phone, // harus format 628xxx
                        'message' => $pesan,
                        'countryCode' => '62',
                    ]);
        }

        $roleNames = $roleUsers->pluck('role')->toArray();
        $roleNamesString = implode(', ', $roleNames);

        LogAktivitasHelper::log('Update Role Surat', "Admin mengupdate role surat masuk dengan nomor {$surat->nomor_surat} ke $roleNamesString");

        return back()->with('success', 'Surat berhasil dikirim ke ' . $roleNamesString);
    }


    public function storeSuratMasuk(Request $request)
    {
        $request->validate([
            'nomor_surat' => 'required|string|max:255',
            'pengirim' => 'required|string|max:255',
            'perihal' => 'required|string',
            'tanggal_surat' => 'required|date',
            'file_surat' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $filePath = null;

        if ($request->hasFile('file_surat')) {
            $filePath = $request->file('file_surat')->store('surat_masuk');

            // Panggil service tanpa static
            $this->fileEncryptionService->encryptFile($filePath);
        }

        $surat = SuratMasuk::create([
            'nomor_surat' => $request->nomor_surat,
            'pengirim' => $request->pengirim,
            'perihal' => $request->perihal,
            'tanggal_surat' => $request->tanggal_surat,
            'status' => 'baru',
            'jenis_surat' => $request->jenis_surat,
            'file_surat' => $filePath,
        ]);

        ArsipSurat::updateOrCreate(
            ['nomor_surat' => $surat->nomor_surat],
            [
                'pengirim' => $surat->pengirim,
                'perihal' => $surat->perihal,
                'tanggal_surat' => $surat->tanggal_surat,
                'jenis_surat' => 'masuk',
                'file_surat' => $surat->file_surat,
                'status' => 'baru',
            ]
        );

        Disposisi::create([
            'id_surat' => $surat->id,

            'jenis_surat' => 'Masuk',
        ]);

        LogAktivitasHelper::log('Tambah Surat Masuk', "Admin menambahkan surat masuk dengan nomor {$surat->nomor_surat}");

        return redirect()->route('admin.suratMasuk')->with('success', 'Surat masuk berhasil ditambahkan dan diarsipkan');
    }

    public function download($id)
    {
        $surat = SuratMasuk::findOrFail($id);

        if ($surat->file_surat && Storage::exists($surat->file_surat)) {
            $decryptedContent = $this->fileEncryptionService->decryptFile($surat->file_surat);

            if ($decryptedContent === false) {
                return redirect()->back()->with('error', 'Gagal mendekripsi file.');
            }

            LogAktivitasHelper::log('Download Surat Masuk', "Admin mendownload file surat masuk dengan nomor {$surat->nomor_surat}");

            return response($decryptedContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . basename($surat->file_surat) . '"');
        }

        return redirect()->back()->with('error', 'File tidak ditemukan.');
    }

    public function updateSuratMasuk(Request $request, $id, FileEncryptionService $fileEncryptionService)
    {
        $surat = SuratMasuk::findOrFail($id);

        $request->validate([
            'nomor_surat' => 'required|string|max:255',
            'pengirim' => 'required|string|max:255',
            'perihal' => 'required|string',
            'tanggal_surat' => 'required|date',
            'status' => 'required|string|in:baru,selesai,ditindaklanjuti,diterima',
            'id_role' => 'required|array',
            'id_role.*' => 'exists:users,id',
            'file_surat' => 'nullable|file|mimes:pdf|max:2048',
            'jenis_surat' => 'required|string'
        ]);

        if ($request->hasFile('file_surat')) {
            if ($surat->file_surat && Storage::exists($surat->file_surat)) {
                Storage::delete($surat->file_surat);
            }

            $filePath = $request->file('file_surat')->store('surat_masuk');
            $fileEncryptionService->encryptFile($filePath);
            $surat->file_surat = $filePath;
        }

        $surat->update($request->except('file_surat', 'id_role'));

        // Sync role tujuan
        $validRoleIds = User::whereIn('id', $request->input('id_role'))->pluck('id')->toArray();
        $surat->roles()->sync($validRoleIds);

        $roleNames = User::whereIn('id', $validRoleIds)->pluck('role')->toArray();
        $roleNamesString = implode(', ', $roleNames);

        // Update arsip
        $arsip = ArsipSurat::where('nomor_surat', $surat->nomor_surat)->first();
        if ($arsip) {
            if ($request->hasFile('file_surat') && isset($filePath)) {
                if ($arsip->file_surat && Storage::exists($arsip->file_surat)) {
                    Storage::delete($arsip->file_surat);
                }
                $arsip->file_surat = $filePath;
            }

            $arsip->update([
                'pengirim' => $surat->pengirim,
                'perihal' => $surat->perihal,
                'tanggal_surat' => $surat->tanggal_surat,
                'file_surat' => $arsip->file_surat,
                'status' => $surat->status === 'selesai' ? 'diarsipkan' : $arsip->status,
            ]);
        }

        LogAktivitasHelper::log('Update Surat Masuk', "Admin memperbarui surat masuk dengan nomor {$surat->nomor_surat}");
        LogAktivitasHelper::log('Update Role Surat', "Admin mengupdate role surat masuk dengan nomor {$surat->nomor_surat} ke $roleNamesString");

        return redirect()->route('admin.suratMasuk')->with('success', 'Surat masuk berhasil diperbarui dan role dikirim ke ' . $roleNamesString);
    }


    public function downloadSuratKeluar($id, FileEncryptionService $fileEncryptionService)
    {
        $adminData = Auth::user();
        $surat = SuratKeluar::findOrFail($id);

        if ($surat->file_surat && Storage::exists($surat->file_surat)) {
            // Log aktivitas download surat keluar
            LogAktivitasHelper::log(
                'Download Surat Keluar',
                "{$adminData->nama} mendownload surat keluar dengan nomor surat: {$surat->nomor_surat}"
            );

            // Dekripsi file sebelum di-download
            $decryptedContent = $fileEncryptionService->decryptFile($surat->file_surat);

            if ($decryptedContent === false) {
                LogAktivitasHelper::log(
                    'Download Surat Keluar Gagal',
                    "{$adminData->nama} gagal mendekripsi surat keluar dengan nomor surat: {$surat->nomor_surat}"
                );

                return redirect()->back()->with('error', 'Gagal mendekripsi file surat keluar.');
            }

            return response($decryptedContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . basename($surat->file_surat) . '"');
        }

        return redirect()->back()->with('error', 'File tidak ditemukan.');
    }


    public function hapusSuratMasuk($id)
    {
        $surat = SuratMasuk::findOrFail($id);

        Disposisi::where('jenis_surat', 'masuk')->where('id_surat', $id)->delete();

        if ($surat->file_surat) {
            Storage::delete($surat->file_surat);
        }

        $surat->delete();

        LogAktivitasHelper::log('Hapus Surat Masuk', "Admin menghapus surat masuk dengan nomor {$surat->nomor_surat}");

        return redirect()->route('admin.suratMasuk')->with('success', 'Surat masuk berhasil dihapus');
    }


    public function arsipSurat(Request $request)
    {
        $adminData = Auth::user();
        $jenisSurat = $request->input('jenis_surat'); // 'masuk', 'keluar', atau null

        $arsipSurats = ArsipSurat::when($jenisSurat, function ($query) use ($jenisSurat) {
            return $query->where('jenis_surat', $jenisSurat);
        })->get();

        LogAktivitasHelper::log('Lihat Data Arsip', 'Admin mengakses halaman arsip surat');

        return view('admin.arsipSurat', compact('arsipSurats', 'adminData', 'jenisSurat'));
    }


    public function downloadArsipSurat($id, FileEncryptionService $fileEncryptionService)
    {
        $arsip = ArsipSurat::findOrFail($id);

        if ($arsip->file_surat && Storage::exists($arsip->file_surat)) {
            $decryptedContent = $fileEncryptionService->decryptFile($arsip->file_surat);

            if ($decryptedContent === false) {
                LogAktivitasHelper::log('Download Arsip Gagal', "Gagal mendekripsi arsip surat nomor: {$arsip->nomor_surat}");

                return redirect()->back()->with('error', 'Gagal mendekripsi file arsip.');
            }

            LogAktivitasHelper::log('Download Arsip', "Mengunduh arsip surat nomor: {$arsip->nomor_surat}");

            return response($decryptedContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . basename($arsip->file_surat) . '"');
        }

        LogAktivitasHelper::log('Download Arsip Gagal', "File arsip surat nomor: {$arsip->nomor_surat} tidak ditemukan");

        return redirect()->back()->with('error', 'File tidak ditemukan.');
    }


    public function updateStatusArsip(Request $request, $id)
    {
        $arsip = ArsipSurat::findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:baru,selesai,ditindaklanjuti,diarsipkan',
        ]);

        $arsip->update(['status' => $request->status]);

        LogAktivitasHelper::log('Update Status Arsip', "Status arsip surat nomor: {$arsip->nomor_surat} diubah menjadi {$request->status}");

        return redirect()->route('admin.arsipSurat')->with('success', 'Status arsip berhasil diperbarui');
    }

    public function updateStatusSuratMasuk(Request $request, $id)
    {
        $surat = SuratMasuk::findOrFail($id);
        $oldStatus = $surat->status;

        $surat->status = $request->status;
        $surat->save();

        if ($request->status === 'selesai') {
            $arsip = ArsipSurat::updateOrCreate(
                ['nomor_surat' => $surat->nomor_surat],
                [
                    'pengirim' => $surat->pengirim,
                    'perihal' => $surat->perihal,
                    'tanggal_surat' => $surat->tanggal_surat,
                    'file_surat' => $surat->file_surat,
                    'jenis_surat' => 'masuk',
                    'status' => 'diarsipkan',
                ]
            );

            LogAktivitasHelper::log('Arsipkan Surat Masuk', "Surat masuk nomor: {$surat->nomor_surat} diarsipkan.");
        } elseif ($request->status === 'baru') {
            if (!Disposisi::where('id_surat', $surat->id)->where('jenis_surat', 'Masuk')->exists()) {
                Disposisi::create([
                    'id_surat' => $surat->id,

                    'jenis_surat' => 'Masuk',
                ]);

                LogAktivitasHelper::log('Tambah Disposisi', "Disposisi baru dibuat untuk surat masuk nomor: {$surat->nomor_surat}");
            }
        }

        LogAktivitasHelper::log('Update Status Surat Masuk', "Status surat masuk nomor: {$surat->nomor_surat} diubah dari {$oldStatus} menjadi {$request->status}");

        return redirect()->back()->with('success', 'Status surat berhasil diperbarui.');
    }

    public function updateArsipSurat(Request $request, $id, FileEncryptionService $fileEncryptionService)
    {
        $arsip = ArsipSurat::findOrFail($id);

        $request->validate([
            'nomor_surat' => 'required|string|max:255',
            'pengirim' => 'required|string|max:255',
            'perihal' => 'required|string',
            'tanggal_surat' => 'required|date',
            'status' => 'required|string|in:baru,selesai,ditindaklanjuti,diarsipkan',
            'file_surat' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $filePath = $arsip->file_surat;

        if ($request->hasFile('file_surat')) {
            // Hapus file lama kalau ada
            if ($arsip->file_surat && Storage::exists($arsip->file_surat)) {
                Storage::delete($arsip->file_surat);
            }

            // Upload file baru
            $filePath = $request->file('file_surat')->store('arsip_surat');

            // Enkripsi file baru
            $fileEncryptionService->encryptFile($filePath);
        }

        // Update data arsip
        $arsip->update([
            'nomor_surat' => $request->nomor_surat,
            'pengirim' => $request->pengirim,
            'perihal' => $request->perihal,
            'tanggal_surat' => $request->tanggal_surat,
            'status' => $request->status,
            'file_surat' => $filePath,
        ]);

        LogAktivitasHelper::log('Update Arsip Surat', "Data arsip surat nomor: {$arsip->nomor_surat} diperbarui.");

        return redirect()->route('admin.arsipSurat')->with('success', 'Arsip surat berhasil diperbarui.');
    }

    public function hapusArsipSurat($id)
    {
        $arsip = ArsipSurat::findOrFail($id);

        if ($arsip->file_surat && Storage::exists($arsip->file_surat)) {
            Storage::delete($arsip->file_surat);
        }

        $arsip->delete();

        LogAktivitasHelper::log('Hapus Arsip Surat', "Data arsip surat nomor: {$arsip->nomor_surat} dihapus.");

        return redirect()->route('admin.arsipSurat')->with('success', 'Arsip surat berhasil dihapus.');
    }

    // Menampilkan daftar user
    public function users()
    {
        $adminData = Auth::user();
        $users = User::with('bidang')
            ->whereIn('role', ['kadis', 'kabid', 'sekretaris', 'pegawai'])
            ->get();

        $bidangs = Bidang::all();

        LogAktivitasHelper::log('Lihat Data User', 'Admin mengakses halaman manajemen user.');

        return view('admin.users', compact('users', 'bidangs', 'adminData'));
    }

    public function storeUsers(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:kadis,kabid,pegawai,sekretaris',
            'id_bidang' => 'nullable|exists:bidangs,id',
            'nama_bidang' => 'nullable|string|max:255',
            'foto_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->nama_bidang) {
            $bidang = Bidang::create(['nama_bidang' => $request->nama_bidang]);
            $id_bidang = $bidang->id;

            LogAktivitasHelper::log('Tambah Bidang Baru', "Bidang baru dengan nama {$request->nama_bidang} ditambahkan saat tambah user.");
        } else {
            $id_bidang = $request->id_bidang;
        }

        $foto_profile = null;
        if ($request->hasFile('foto_profile')) {
            $foto_profile = $request->file('foto_profile')->store('foto_profile', 'public');
        }

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'id_bidang' => $id_bidang,
            'foto_profile' => $foto_profile,
        ]);

        LogAktivitasHelper::log('Tambah User', "User baru bernama {$user->nama} ditambahkan dengan role {$user->role}");

        return redirect()->route('admin.users')->with('success', 'User berhasil ditambahkan!');
    }

    public function updateUsers(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:kadis,kabid,pegawai,sekretaris',
            'id_bidang' => 'nullable|exists:bidangs,id',
            'nama_bidang' => 'nullable|string|max:255',
            'foto_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->nama_bidang) {
            $bidang = Bidang::create(['nama_bidang' => $request->nama_bidang]);
            $id_bidang = $bidang->id;

            LogAktivitasHelper::log('Tambah Bidang Baru', "Bidang baru dengan nama {$request->nama_bidang} ditambahkan saat edit user.");
        } else {
            $id_bidang = $request->id_bidang;
        }

        if ($request->hasFile('foto_profile')) {
            if ($user->foto_profile) {
                Storage::disk('public')->delete($user->foto_profile);
            }
            $user->foto_profile = $request->file('foto_profile')->store('foto_profile', 'public');
        }

        $user->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'role' => $request->role,
            'id_bidang' => $id_bidang,
            'foto_profile' => $user->foto_profile,
        ]);

        LogAktivitasHelper::log('Update User', "Data user bernama {$user->nama} diperbarui.");

        return redirect()->route('admin.users')->with('success', 'User berhasil diperbarui!');
    }

    public function hapusUsers($id)
    {
        $user = User::findOrFail($id);

        if ($user->foto_profile) {
            Storage::disk('public')->delete($user->foto_profile);
        }

        $user->delete();

        LogAktivitasHelper::log('Hapus User', "User bernama {$user->nama} dihapus dari sistem.");

        return redirect()->route('admin.users')->with('success', 'User berhasil dihapus');
    }

    public function profileAdmin()
    {
        $adminData = Auth::user();

        LogAktivitasHelper::log('Lihat Profil Admin', 'Admin mengakses halaman profil.');

        return view('admin.profile', compact('adminData'));
    }

    public function updateProfileAdmin(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'foto_profile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user->nama = $request->nama;
        $user->email = $request->email;

        if ($request->hasFile('foto_profile')) {
            if ($user->foto_profile) {
                Storage::delete($user->foto_profile);
            }
            $path = $request->file('foto_profile')->store('foto_profile', 'public');
            $user->foto_profile = $path;
        }

        $user->save();

        LogAktivitasHelper::log('Update Profil', 'Admin memperbarui profilnya.');

        return redirect()->route('admin.profile')->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePasswordAdmin(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama yang Anda masukkan tidak cocok.']);
        }

        $user->password = \Hash::make($request->new_password);
        $user->save();

        LogAktivitasHelper::log('Update Password', 'Admin mengganti password akun.');

        return redirect()->route('admin.profile')->with('success', 'Password berhasil diperbarui!');
    }

    public function storeSuratKeluar(Request $request, FileEncryptionService $fileEncryptionService)
    {
        $adminData = Auth::user();

        $request->validate([
            'nomor_surat' => 'required|string|max:255',
            'pengirim' => 'required|string|max:255',
            'tujuan' => 'required|string|max:255',
            'perihal' => 'required|string',
            'tanggal_surat' => 'required|date',
            'file_surat' => 'nullable|file|mimes:pdf|max:2048',
            'jenis_surat' => 'required|string|max:255'
        ]);

        $filePath = null;

        if ($request->hasFile('file_surat')) {
            $filePath = $request->file('file_surat')->store('surat_keluar');

            // Langsung enkripsi file setelah upload
            $fileEncryptionService->encryptFile($filePath);
        }

        $surat = SuratKeluar::create([
            'nomor_surat' => $request->nomor_surat,
            'pengirim' => $request->pengirim,
            'tujuan' => $request->tujuan,
            'perihal' => $request->perihal,
            'tanggal_surat' => $request->tanggal_surat,
            'status' => 'menunggu',
            'jenis_surat' => $request->jenis_surat,
            'file_surat' => $filePath,
        ]);

        ArsipSurat::updateOrCreate(
            ['nomor_surat' => $surat->nomor_surat],
            [
                'pengirim' => $surat->pengirim,
                'perihal' => $surat->perihal,
                'tanggal_surat' => $surat->tanggal_surat,
                'jenis_surat' => 'keluar',
                'file_surat' => $surat->file_surat,
                'status' => 'baru',
            ]
        );

        Disposisi::create([
            'id_surat' => $surat->id,

            'jenis_surat' => 'Keluar',
        ]);

        LogAktivitasHelper::log('Tambah Surat Keluar', 'Menambahkan surat keluar nomor: ' . $request->nomor_surat);

        return redirect()->route('admin.suratKeluar', compact('adminData'))->with('success', 'Surat keluar berhasil ditambahkan dan diarsipkan');
    }

    public function updateSuratKeluar(Request $request, $id, FileEncryptionService $fileEncryptionService)
    {
        $surat = SuratKeluar::findOrFail($id);

        $request->validate([
            'nomor_surat' => 'required|string|max:255',
            'pengirim' => 'required|string|max:255',
            'tujuan' => 'required|string|max:255',
            'perihal' => 'required|string',
            'tanggal_surat' => 'required|date',
            'status' => 'required|string|in:menunggu,disetujui,ditolak',
            'jenis_surat' => 'required|string|max:255',
            'file_surat' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        if ($request->hasFile('file_surat')) {
            // Hapus file lama kalau ada
            if ($surat->file_surat && Storage::exists($surat->file_surat)) {
                Storage::delete($surat->file_surat);
            }

            // Simpan file baru
            $filePath = $request->file('file_surat')->store('surat_keluar');

            // Enkripsi file yang baru disimpan
            $fileEncryptionService->encryptFile($filePath);

            // Update file di surat keluar
            $surat->file_surat = $filePath;
        }

        // Update data surat keluar
        $surat->update($request->except('file_surat'));

        $surat->update([
            'nomor_surat' => $request->nomor_surat,
            'pengirim' => $request->pengirim,
            'tujuan' => $request->tujuan,
            'perihal' => $request->perihal,
            'tanggal_surat' => $request->tanggal_surat,
            'jenis_surat' => $request->jenis_surat,
            'status' => $request->status,
        ]);

        ArsipSurat::updateOrCreate(
            ['nomor_surat' => $surat->nomor_surat],
            [
                'pengirim' => $surat->pengirim,
                'perihal' => $surat->perihal,
                'tanggal_surat' => $surat->tanggal_surat,
                'file_surat' => $surat->file_surat,
                'status' => $surat->status === 'selesai' ? 'diarsipkan' : 'baru',
            ]
        );

        LogAktivitasHelper::log('Update Surat Keluar', 'Memperbarui surat keluar nomor: ' . $request->nomor_surat);

        return redirect()->route('admin.suratKeluar')->with('success', 'Surat keluar berhasil diperbarui');
    }

    public function hapusSuratKeluar($id)
    {
        $surat = SuratKeluar::findOrFail($id);

        if ($surat->file_surat && Storage::exists($surat->file_surat)) {
            Storage::delete($surat->file_surat);
        }

        $surat->delete();

        LogAktivitasHelper::log('Hapus Surat Keluar', 'Menghapus surat keluar nomor: ' . $surat->nomor_surat);

        return redirect()->route('admin.suratKeluar')->with('success', 'Surat keluar berhasil dihapus');
    }

    public function updateStatusSuratKeluar(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu,disetujui,ditolak',
        ]);

        $surat = SuratKeluar::findOrFail($id);
        $surat->status = $request->status;
        $surat->save();

        if ($request->status === 'disetujui') {
            ArsipSurat::updateOrCreate(
                ['nomor_surat' => $surat->nomor_surat],
                [
                    'pengirim' => $surat->pengirim,
                    'penerima' => Auth::user()->nama,
                    'perihal' => $surat->perihal,
                    'tanggal_surat' => $surat->tanggal_surat,
                    'file_surat' => $surat->file_surat,
                    'jenis_surat' => 'keluar',
                    'status' => 'diarsipkan',
                ]
            );
        }

        LogAktivitasHelper::log('Update Status Surat', 'Ubah status surat keluar nomor: ' . $surat->nomor_surat . ' ke ' . $request->status);

        return redirect()->back()->with('success', 'Status surat berhasil diperbarui.');
    }

    public function suratKeluar()
    {
        LogAktivitasHelper::log('Akses Surat Keluar', 'Admin membuka halaman surat keluar');

        $kadis = User::where('role', 'kadis')->first();
        $sekretaris = User::where('role', 'sekretaris')->first();
        $kabid = User::where('role', 'kabid')->first();
        $surats = SuratKeluar::all();
        $adminData = Auth::user();

        return view('admin.suratKeluar', compact('surats', 'adminData', 'kadis', 'sekretaris', 'kabid'));
    }



    public function laporan(Request $request)
    {
        $adminData = Auth::user();

        // Ambil rentang tanggal dari request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Query surat masuk
        $suratMasuk = SuratMasuk::query();
        // Query surat keluar
        $suratKeluar = SuratKeluar::query();

        // Filter berdasarkan rentang tanggal pada created_at jika diberikan
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay(); // Mulai dari 00:00:00
            $end = Carbon::parse($endDate)->endOfDay(); // Sampai 23:59:59

            $suratMasuk->whereBetween('created_at', [$start, $end]);
            $suratKeluar->whereBetween('created_at', [$start, $end]);
        }

        // Dapatkan data hasil filter
        $suratMasuk = $suratMasuk->get();
        $suratKeluar = $suratKeluar->get();

        return view('admin.laporan', compact('adminData', 'suratMasuk', 'suratKeluar'));
    }

    public function cetakPDF(Request $request)
    {
        $adminData = Auth::user();

        // Ambil rentang tanggal dari request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Jika tidak ada tanggal, set default NULL
        if (!$startDate || !$endDate) {
            $startDate = null;
            $endDate = null;
        }

        // Query surat masuk & keluar
        $suratMasuk = SuratMasuk::query();
        $suratKeluar = SuratKeluar::query();

        // Filter berdasarkan rentang tanggal pada `created_at` jika diberikan
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            $suratMasuk->whereBetween('created_at', [$start, $end]);
            $suratKeluar->whereBetween('created_at', [$start, $end]);
        }

        // Ambil data hasil filter
        $suratMasuk = $suratMasuk->get();
        $suratKeluar = $suratKeluar->get();

        // Debugging untuk memastikan data terkirim
        // dd($startDate, $endDate, $suratMasuk, $suratKeluar);

        // Load view ke PDF
        $pdf = Pdf::loadView('admin.laporan_pdf', compact('adminData', 'suratMasuk', 'suratKeluar', 'startDate', 'endDate'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('Laporan_Surat_' . Carbon::now()->format('Ymd_His') . '.pdf');
    }

    public function bidang()
    {
        $adminData = Auth::user();
        $bidangs = Bidang::all();
        return view('admin.bidang', compact('adminData', 'bidangs'));
    }

    public function storeBidang(Request $request)
    {
        $request->validate([
            'nama_bidang' => 'required|unique:bidangs,nama_bidang|max:255'
        ]);

        Bidang::create(['nama_bidang' => $request->nama_bidang]);

        return redirect()->route('admin.bidang')->with('success', 'Bidang berhasil ditambahkan.');
    }

    public function updateBidang(Request $request, $id)
    {
        $bidang = Bidang::findOrFail($id);
        $request->validate([
            'nama_bidang' => 'required|unique:bidangs,nama_bidang,' . $bidang->id . '|max:255'
        ]);

        $bidang->update(['nama_bidang' => $request->nama_bidang]);

        return redirect()->route('admin.bidang')->with('success', 'Bidang berhasil diperbarui.');
    }

    public function hapusBidang(Bidang $bidang)
    {
        $bidang->delete();
        return redirect()->route('admin.bidang')->with('success', 'Bidang berhasil dihapus.');
    }

}