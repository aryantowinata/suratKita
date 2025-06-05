<?php

namespace App\Http\Controllers;

use App\Helpers\LogAktivitasHelper;
use App\Models\Bidang;
use App\Models\Instruksi;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SuratMasuk;
use App\Models\Disposisi;
use App\Models\ArsipSurat;
use App\Models\SuratKeluar;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Services\FileEncryptionService;

class PimpinanController extends Controller
{
    public function dashboardPimpinan()
    {
        $adminData = Auth::user();
        $totalSuratMasuk = Disposisi::where('jenis_surat', 'masuk')
            ->whereHas('suratMasuk.roles', function ($query) use ($adminData) {
                // Hanya surat yang ditujukan ke user
                $query->where('users.id', $adminData->id);

                // Filter bidang sesuai bidang user jika ada
                $query->where('users.id_bidang', $adminData->id_bidang);
            })
            ->with('suratMasuk')
            ->count();

        // Log aktivitas kunjungan dashboard
        LogAktivitasHelper::log('Akses Dashboard', "{$adminData->nama} mengakses halaman dashboard");

        return response()->view('pimpinan.dashboard', compact('adminData', 'totalSuratMasuk'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function logout(Request $request)
    {
        $adminData = Auth::user();
        // Log aktivitas logout
        LogAktivitasHelper::log('Logout', "{$adminData->nama} melakukan logout");

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/auth/login');
    }

    public function updateInstruksi(Request $request, $disposisiId)
    {
        $disposisi = Disposisi::findOrFail($disposisiId);

        $instruksiIds = $request->input('instruksi_ids', []); // array of selected instruksi IDs

        // Sync instruksi
        $disposisi->instruksis()->sync($instruksiIds);

        return redirect()->back()->with('success', 'Instruksi berhasil diperbarui.');
    }


    public function disposisiSuratMasuk()
    {
        $adminData = Auth::user();
        $bidangs = Bidang::all();
        $allInstruksis = Instruksi::all();
        $disposisis = Disposisi::where('jenis_surat', 'masuk')
            ->whereHas('suratMasuk.roles', function ($query) use ($adminData) {
                // Hanya surat yang ditujukan ke user
                $query->where('users.id', $adminData->id);

                // Filter bidang sesuai bidang user jika ada
                $query->where('users.id_bidang', $adminData->id_bidang);
            })
            ->with('suratMasuk')
            ->get();

        // Log aktivitas akses disposisi
        LogAktivitasHelper::log('Lihat Disposisi Surat Masuk', "{$adminData->nama} mengakses daftar disposisi surat masuk");

        return view('pimpinan.disposisiSuratMasuk', compact('disposisis', 'adminData', 'bidangs', 'allInstruksis'));
    }



    public function download($id, FileEncryptionService $fileEncryptionService)
    {
        $adminData = Auth::user();
        $surat = SuratMasuk::findOrFail($id);

        if ($surat->file_surat && Storage::exists($surat->file_surat)) {
            // Log aktivitas download surat
            LogAktivitasHelper::log('Download Surat Masuk', "{$adminData->nama} mendownload file surat masuk dengan nomor: " . $surat->nomor_surat);

            // Dekripsi file sebelum didownload
            $decryptedContent = $fileEncryptionService->decryptFile($surat->file_surat);

            if ($decryptedContent === false) {
                LogAktivitasHelper::log('Download Surat Masuk Gagal', "{$adminData->nama} gagal mendekripsi file surat masuk dengan nomor: " . $surat->nomor_surat);

                return redirect()->back()->with('error', 'Gagal mendekripsi file surat.');
            }

            return response($decryptedContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . basename($surat->file_surat) . '"');
        }

        return redirect()->back()->with('error', 'File tidak ditemukan.');
    }


    public function updateStatusSuratMasuk(Request $request, $id)
    {
        $adminData = Auth::user();
        $request->validate([
            'status' => 'required|in:baru,ditindaklanjuti,selesai,diarsipkan,diterima',
        ]);

        $surat = SuratMasuk::findOrFail($id);
        $oldStatus = $surat->status;
        $surat->status = $request->status;
        $surat->save();

        // Log aktivitas update status
        LogAktivitasHelper::log(
            'Update Status Surat Masuk',
            "{$adminData->nama} mengubah status surat masuk nomor: {$surat->nomor_surat} dari '{$oldStatus}' menjadi '{$request->status}'"
        );

        if ($request->status === 'diarsipkan') {
            $namaPenerima = Auth::user() ? Auth::user()->nama : 'Sistem';

            ArsipSurat::updateOrCreate(
                ['nomor_surat' => $surat->nomor_surat],
                [
                    'pengirim' => $surat->pengirim,
                    'penerima' => $namaPenerima,
                    'perihal' => $surat->perihal,
                    'tanggal_surat' => $surat->tanggal_surat,
                    'file_surat' => $surat->file_surat,
                    'jenis_surat' => 'masuk',
                    'status' => 'diarsipkan',
                ]
            );

            // Log aktivitas arsipkan surat
            LogAktivitasHelper::log(
                'Arsipkan Surat Masuk',
                "Surat masuk dengan nomor: {$surat->nomor_surat} telah diarsipkan"
            );
        }

        return redirect()->back()->with('success', 'Status surat berhasil diperbarui.');
    }


    public function updateBidang(Request $request, $id)
    {
        $adminData = Auth::user();
        $disposisi = Disposisi::findOrFail($id);
        $disposisi->id_pengirim = Auth::id();

        // Sinkronisasi bidang-bidang yang dipilih
        $disposisi->bidangs()->sync($request->bidang_ids);

        LogAktivitasHelper::log(
            'Update Bidang Disposisi',
            "{$adminData->nama} mengupdate bidang disposisi ID: {$disposisi->id} ke bidang ID: " . implode(',', $request->bidang_ids)
        );

        return redirect()->back()->with('success', 'Bidang berhasil diperbarui.');
    }


    public function profilePimpinan()
    {
        $adminData = Auth::user();

        // Log aktivitas akses profil
        LogAktivitasHelper::log('Akses Profil', 'Pimpinan mengakses halaman profil');

        return view('pimpinan.profile', compact('adminData'));
    }

    public function updateProfilePimpinan(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'foto_profile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $oldProfile = $user->foto_profile;

        $user->nama = $request->nama;
        $user->email = $request->email;

        if ($request->hasFile('foto_profile')) {
            if ($oldProfile) {
                Storage::delete($oldProfile);
            }

            $path = $request->file('foto_profile')->store('foto_profile', 'public');
            $user->foto_profile = $path;
        }

        $user->save();

        // Log aktivitas update profil
        LogAktivitasHelper::log(
            'Update Profil',
            "{$user->nama} memperbarui profil, termasuk nama, email, dan foto profil"
        );

        return redirect()->route('pimpinan.profile')->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePasswordPimpinan(Request $request)
    {
        $adminData = Auth::user();
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

        // Log aktivitas update password
        LogAktivitasHelper::log('Update Password', "{$adminData->nama} memperbarui password");

        return redirect()->route('pimpinan.profile')->with('success', 'Password berhasil diperbarui!');
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


    public function disposisiSuratKeluar()
    {
        $adminData = Auth::user();
        $bidangs = Bidang::all();
        $allInstruksis = Instruksi::all();
        $disposisis = Disposisi::where('jenis_surat', 'keluar')
            ->with('suratKeluar')
            ->get();

        // Log aktivitas akses halaman disposisi surat keluar
        LogAktivitasHelper::log('Akses Disposisi Surat Keluar', "{$adminData->nama} mengakses halaman disposisi surat keluar");

        return view('pimpinan.disposisiSuratKeluar', compact('disposisis', 'adminData', 'bidangs', 'allInstruksis'));
    }

    public function updateStatusSuratKeluar(Request $request, $id)
    {
        $adminData = Auth::user();
        $request->validate([
            'status' => 'required|in:menunggu,ditolak,disetujui',
        ]);

        $surat = SuratKeluar::findOrFail($id);
        $oldStatus = $surat->status;
        $surat->status = $request->status;
        $surat->save();

        // Log aktivitas update status surat keluar
        LogAktivitasHelper::log(
            'Update Status Surat Keluar',
            "{$adminData->nama} mengubah status surat keluar (Nomor: {$surat->nomor_surat}) dari '{$oldStatus}' menjadi '{$surat->status}'"
        );

        if ($request->status === 'disetujui') {
            $namaPenerima = Auth::user() ? Auth::user()->nama : 'Sistem';

            ArsipSurat::updateOrCreate(
                ['nomor_surat' => $surat->nomor_surat],
                [
                    'pengirim' => $surat->pengirim,
                    'penerima' => $namaPenerima,
                    'perihal' => $surat->perihal,
                    'tanggal_surat' => $surat->tanggal_surat,
                    'file_surat' => $surat->file_surat,
                    'jenis_surat' => 'keluar',
                    'status' => 'diarsipkan',
                ]
            );

            // Log tambahan jika surat diarsipkan
            LogAktivitasHelper::log(
                'Arsip Surat Keluar',
                "Surat keluar dengan nomor surat {$surat->nomor_surat} diarsipkan setelah disetujui"
            );
        }

        return redirect()->back()->with('success', 'Status surat berhasil diperbarui.');
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
                'file_surat' => $filePath,
                'status' => 'baru',
            ]
        );

        Disposisi::create([
            'id_surat' => $surat->id,
            'jenis_surat' => 'Keluar',
        ]);

        // Log aktivitas tambah surat keluar
        LogAktivitasHelper::log(
            'Tambah Surat Keluar',
            "{$adminData->nama} menambahkan surat keluar baru dengan nomor: {$surat->nomor_surat}"
        );

        return redirect()->route('pimpinan.disposisiSuratKeluar', compact('adminData'))->with('success', 'Surat keluar berhasil ditambahkan dan diarsipkan');
    }

    public function storeInstruksi(Request $request)
    {

        // Validasi input
        $request->validate([
            'nama_instruksi' => 'required|string|max:255',
        ]);

        // Simpan ke DB
        Instruksi::create([
            'nama_instruksi' => $request->nama_instruksi,
        ]);

        // Redirect kembali ke halaman pimpinan surat masuk (atau halaman sebelumnya)
        return redirect()->back()->with('success', 'Instruksi berhasil ditambahkan.');
    }
    public function updateDisposisiSuratKeluar(Request $request, $id, FileEncryptionService $fileEncryptionService)
    {
        $adminData = Auth::user();
        $disposisi = Disposisi::findOrFail($id);

        $request->validate(rules: [
            'nomor_surat' => 'required|string|max:255',
            'pengirim' => 'required|string|max:255',
            'perihal' => 'required|string|max:255',
            'jenis_surat' => 'required|string|max:255',
            'file_surat' => 'nullable|mimes:pdf,doc,docx|max:2048',

            'status_surat' => 'required|in:menunggu,ditolak,disetujui',
            'id_bidang' => 'nullable|exists:bidangs,id',
        ]);

        // Update Surat Keluar
        $suratKeluar = SuratKeluar::find($disposisi->id_surat);

        $suratKeluar->update([
            'nomor_surat' => $request->nomor_surat,
            'pengirim' => $request->pengirim,
            'perihal' => $request->perihal,
            'jenis_surat' => $request->jenis_surat,
            'status' => $request->status_surat,
        ]);

        // Upload file jika ada perubahan
        if ($request->hasFile('file_surat')) {
            // Hapus file lama kalau ada
            if ($suratKeluar->file_surat && Storage::exists($suratKeluar->file_surat)) {
                Storage::delete($suratKeluar->file_surat);
            }

            // Simpan file baru
            $filePath = $request->file('file_surat')->store('surat_keluar');

            // Enkripsi file yang baru disimpan
            $fileEncryptionService->encryptFile($filePath);

            // Update file di surat keluar
            $suratKeluar->file_surat = $filePath;
        }

        // Update data surat keluar
        $suratKeluar->update($request->except('file_surat'));
        // Update Disposisi
        $disposisi->update([
            'instruksi' => $request->instruksi,
            'status' => $request->status_disposisi,
            'id_bidang' => $request->id_bidang,
        ]);

        LogAktivitasHelper::log(
            'Update Surat Keluar',
            "{$adminData->nama} menambahkan surat keluar baru dengan nomor: {$suratKeluar->nomor_surat}"
        );

        return redirect()->route('pimpinan.disposisiSuratKeluar')->with('success', 'Disposisi dan Surat Keluar berhasil diperbarui.');
    }

    public function hapusDisposisiSuratKeluar($id)
    {
        $adminData = Auth::user();
        $disposisi = Disposisi::findOrFail($id);

        $suratKeluar = SuratKeluar::find($disposisi->id_surat);

        if ($suratKeluar->file_surat && Storage::exists($suratKeluar->file_surat)) {
            Storage::delete($suratKeluar->file_surat);

            $suratKeluar->delete();
        }

        $disposisi->delete();

        LogAktivitasHelper::log('Hapus Surat Keluar', "{$adminData->nama} Menghapus surat keluar nomor: " . ($suratKeluar ? $suratKeluar->nomor_surat : 'Tidak Diketahui'));

        return redirect()->route('pimpinan.disposisiSuratKeluar')->with('success', 'Disposisi dan Surat Keluar berhasil dihapus.');
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

        return view('pimpinan.laporan', compact('adminData', 'suratMasuk', 'suratKeluar'));
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
        $pdf = Pdf::loadView('pimpinan.laporan_pdf', compact('adminData', 'suratMasuk', 'suratKeluar', 'startDate', 'endDate'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('Laporan_Surat_' . Carbon::now()->format('Ymd_His') . '.pdf');
    }
}