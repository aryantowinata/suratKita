<?php

namespace App\Http\Controllers;

use App\Models\Instruksi;
use Illuminate\Http\Request;
use App\Helpers\LogAktivitasHelper;
use Illuminate\Support\Facades\Auth;
use App\Models\SuratMasuk;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\Bidang;
use App\Models\Disposisi;
use App\Models\ArsipSurat;
use App\Models\SuratKeluar;

class PegawaiController extends Controller
{
    public function dashboardPegawai()
    {

        $adminData = Auth::user();
        $totalSuratMasuk = SuratMasuk::count();
        $totalDisposisi = SuratMasuk::where('status', 'selesai')->count();
        $totalUsers = User::whereIn('role', ['pimpinan', 'pegawai'])->count();

        return response()->view('pegawai.dashboard', compact('adminData', 'totalSuratMasuk', 'totalUsers', 'totalDisposisi'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/auth/login');
    }

    public function profilePegawai()
    {
        $adminData = Auth::user();
        return view('pegawai.profile', compact('adminData'));
    }

    public function updateProfilePegawai(Request $request)
    {
        $user = Auth::user();

        // Validasi input  
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'foto_profile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi untuk foto profil  
        ]);

        // Update data pengguna  
        $user->nama = $request->nama;
        $user->email = $request->email;

        // Jika ada file yang diupload  
        if ($request->hasFile('foto_profile')) {
            // Hapus foto profil lama jika ada  
            if ($user->foto_profile) {
                Storage::delete($user->foto_profile);
            }

            // Simpan foto profil baru  
            $path = $request->file('foto_profile')->store('foto_profile', 'public');
            $user->foto_profile = $path;
        }

        $user->save();

        return redirect()->route('pegawai.profile')->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePasswordPegawai(Request $request)
    {
        // Ambil data user yang sedang login
        $user = Auth::user();

        // Validasi input
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed', // Setidaknya 8 karakter dan password konfirmasi harus cocok
        ]);

        // Cek apakah password lama yang dimasukkan cocok dengan password yang ada di database
        if (!\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama yang Anda masukkan tidak cocok.']);
        }

        // Update password jika valid
        $user->password = \Hash::make($request->new_password);
        $user->save();

        // Redirect kembali ke halaman profile dengan pesan sukses
        return redirect()->route('pegawai.profile')->with('success', 'Password berhasil diperbarui!');
    }

    public function disposisiSuratMasuk()
    {
        $adminData = Auth::user();
        $allInstruksis = Instruksi::all();

        // Ambil hanya disposisi yang jenis_surat = masuk, dan sertakan data surat masuk
        $disposisis = Disposisi::where('jenis_surat', 'masuk')
            ->where('id_bidang', Auth::user()->id_bidang) // Sesuai bidang user yang login
            ->with('suratMasuk') // Tetap ambil relasi ke surat masuk
            ->get();

        // Log aktivitas akses disposisi
        LogAktivitasHelper::log('Lihat Disposisi Surat Masuk', "{$adminData->nama} mengakses daftar disposisi surat masuk");

        return view('pegawai.disposisiSuratMasuk', compact('disposisis', 'adminData', 'allInstruksis'));
    }

    public function download($id)
    {
        $surat = SuratMasuk::findOrFail($id);

        if ($surat->file_surat && Storage::exists($surat->file_surat)) {
            return Storage::download($surat->file_surat);
        }

        return redirect()->back()->with('error', 'File tidak ditemukan.');
    }

    public function updateStatusSuratMasuk(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'status' => 'required|in:baru,diterima,ditindaklanjuti,selesai,diarsipkan',
        ]);

        $surat = SuratMasuk::findOrFail($id);
        $surat->status = $request->status;
        $surat->save();

        // Jika status diubah menjadi 'diarsipkan'
        if ($request->status === 'diarsipkan') {
            // Pastikan pengguna telah login sebelum mengambil nama
            $namaPenerima = Auth::user() ? Auth::user()->nama : 'Sistem';

            // Gunakan updateOrCreate untuk menghindari pengecekan manual
            ArsipSurat::updateOrCreate(
                ['nomor_surat' => $surat->nomor_surat], // Kriteria pencarian
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
        }

        return redirect()->back()->with('success', 'Status surat berhasil diperbarui.');
    }


    // public function updateStatusDisposisi(Request $request, $id)
    // {
    //     $disposisi = Disposisi::findOrFail($id);
    //     $disposisi->status = $request->status;
    //     $disposisi->save();

    //     return redirect()->back()->with('success', 'Status disposisi berhasil diperbarui.');
    // }

    public function updateBidang(Request $request, $id)
    {
        $disposisi = Disposisi::findOrFail($id);
        $disposisi->id_pengirim = Auth::id();
        $disposisi->id_bidang = $request->id_bidang; // Simpan ID, bukan nama bidang
        $disposisi->save();

        return redirect()->back()->with('success', 'Bidang berhasil diperbarui.');
    }

    public function hapusDisposisiSuratMasuk($id)
    {
        $disposisi = Disposisi::findOrFail($id);

        // Hapus data dari database
        $disposisi->delete();

        return redirect()->route('pimpinan.disposisiSuratMasuk')->with('success', 'Disposisi Surat masuk berhasil dihapus');
    }

    public function updateInstruksi(Request $request, $id)
    {
        $request->validate([
            'instruksi' => 'nullable|string|max:255',
        ]);

        $disposisi = Disposisi::findOrFail($id);
        $disposisi->instruksi = $request->instruksi;
        $disposisi->save();

        return redirect()->back()->with('success', 'Instruksi berhasil diperbarui.');
    }

    public function disposisiSuratKeluar()
    {
        $adminData = Auth::user();
        $allInstruksis = Instruksi::all();

        // Ambil hanya disposisi yang jenis_surat = masuk, dan sertakan data surat masuk
        $disposisis = Disposisi::where('jenis_surat', 'keluar')
            ->where('id_bidang', Auth::user()->id_bidang) // Sesuai bidang user yang login
            ->with('suratKeluar') // Tetap ambil relasi ke surat masuk
            ->get();


        return view('pegawai.disposisiSuratKeluar', compact('disposisis', 'adminData', 'allInstruksis'));
    }

    public function hapusDisposisiSuratKeluar($id)
    {
        $disposisi = Disposisi::findOrFail($id);

        // Hapus data dari database
        $disposisi->delete();

        return redirect()->route('pimpinan.disposisiSuratKeluar')->with('success', 'Disposisi Surat keluar berhasil dihapus');
    }

    public function updateStatusSuratKeluar(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'status' => 'required|in:menunggu,ditolak,disetujui',
        ]);

        $surat = SuratKeluar::findOrFail($id);
        $surat->status = $request->status;
        $surat->save();

        // Jika status diubah menjadi 'diarsipkan'
        if ($request->status === 'disetujui') {
            // Pastikan pengguna telah login sebelum mengambil nama
            $namaPenerima = Auth::user() ? Auth::user()->nama : 'Sistem';

            // Gunakan updateOrCreate untuk menghindari pengecekan manual
            ArsipSurat::updateOrCreate(
                ['nomor_surat' => $surat->nomor_surat], // Kriteria pencarian
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
        }

        return redirect()->back()->with('success', 'Status surat berhasil diperbarui.');
    }

    public function downloadSuratKeluar($id)
    {
        $surat = SuratKeluar::findOrFail($id);

        if ($surat->file_surat && Storage::exists($surat->file_surat)) {
            return Storage::download($surat->file_surat);
        }

        return redirect()->back()->with('error', 'File tidak ditemukan.');
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

        return view('pegawai.laporan', compact('adminData', 'suratMasuk', 'suratKeluar'));
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
        $pdf = Pdf::loadView('pegawai.laporan_pdf', compact('adminData', 'suratMasuk', 'suratKeluar', 'startDate', 'endDate'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('Laporan_Surat_' . Carbon::now()->format('Ymd_His') . '.pdf');
    }

}