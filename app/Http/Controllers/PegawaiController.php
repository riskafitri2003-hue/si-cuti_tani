<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\SaldoCuti;
use App\Models\User;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $pegawais = Pegawai::with(['user', 'saldoCutis'])
            ->when($request->q, function ($query, $q) {
                $query->where('nama', 'like', "%{$q}%")
                    ->orWhere('nip', 'like', "%{$q}%");
            })
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('pegawai.index', compact('pegawais'));
    }

    public function create()
    {
        return view('pegawai.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'string', 'max:50', 'unique:pegawais,nip'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'unit_kerja' => ['nullable', 'string', 'max:255'],
            'masa_kerja' => ['nullable', 'string', 'max:100'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'no_telpon' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'wa' => ['nullable', 'string', 'max:20'],
            'fonnte_device_id' => ['nullable', 'string', 'max:255'],
        ]);

        $pegawai = Pegawai::create($data);

        SaldoCuti::create([
            'nip' => $pegawai->nip,
            'jenis_cuti' => 1, // Cuti Tahunan
            'saldo_n2' => 0,
            'saldo_n1' => 6,
            'saldo_n' => 12,
        ]);

        if ($request->filled('password')) {
            User::create([
                'nama' => $data['nama'],
                'nip' => $pegawai->nip,
                'password' => $request->password,
                'role' => $request->role ?? 'pegawai',
            ]);
        }

        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    public function createAccountForm(Pegawai $pegawai)
    {
        if ($pegawai->user) {
            return redirect()->route('pegawai.index')->with('error', 'Pegawai ini sudah memiliki akun.');
        }

        return view('pegawai.create_account', compact('pegawai'));
    }

    public function createAccount(Request $request, Pegawai $pegawai)
    {
        if ($pegawai->user) {
            return redirect()->route('pegawai.index')->with('error', 'Pegawai ini sudah memiliki akun.');
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'min:3', 'confirmed'],
            'role' => ['required', 'string', 'in:pegawai,admin,atasan_langsung,kasubag,sekretaris,kepala_dinas,sekda,walikota'],
        ]);

        User::create([
            'nama' => $pegawai->nama,
            'nip' => $pegawai->nip,
            'password' => $data['password'],
            'role' => $data['role'],
        ]);

        return redirect()->route('pegawai.index')->with('success', 'Akun berhasil dibuat untuk ' . $pegawai->nama . '.');
    }

    public function edit(Pegawai $pegawai)
    {
        $pegawai->load('saldoCutis');

        return view('pegawai.edit', compact('pegawai'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'string', 'max:50', 'unique:pegawais,nip,' . $pegawai->nip . ',nip'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'unit_kerja' => ['nullable', 'string', 'max:255'],
            'masa_kerja' => ['nullable', 'string', 'max:100'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'no_telpon' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'wa' => ['nullable', 'string', 'max:20'],
            'fonnte_device_id' => ['nullable', 'string', 'max:255'],
        ]);

        $pegawai->update($data);

        // Update saldo cuti (bagian V) jika dikirim
        if ($request->has('saldo')) {
            SaldoCuti::updateOrCreate(
                ['nip' => $pegawai->nip],
                [
                    'jenis_cuti' => 1,
                    'saldo_n2' => $request->saldo['saldo_n2'] ?? 0,
                    'saldo_n1' => $request->saldo['saldo_n1'] ?? 0,
                    'saldo_n' => $request->saldo['saldo_n'] ?? 0,
                    'keterangan_n2' => $request->saldo['keterangan_n2'] ?? null,
                    'keterangan_n1' => $request->saldo['keterangan_n1'] ?? null,
                    'keterangan_n' => $request->saldo['keterangan_n'] ?? null,
                ]
            );
        }

        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function kelolaSaldo()
    {
        $pegawais = Pegawai::with('saldoCutis')->orderBy('nama')->paginate(20);

        return view('pegawai.kelola_saldo', compact('pegawais'));
    }

    public function updateSaldo(Request $request)
    {
        $request->validate([
            'saldo' => ['required', 'array'],
        ]);

        foreach ($request->saldo as $nip => $row) {
            SaldoCuti::updateOrCreate(
                ['nip' => $nip],
                [
                    'jenis_cuti' => 1,
                    'saldo_n2' => $row['saldo_n2'] ?? 0,
                    'saldo_n1' => $row['saldo_n1'] ?? 0,
                    'saldo_n' => $row['saldo_n'] ?? 0,
                    'keterangan_n2' => $row['keterangan_n2'] ?? null,
                    'keterangan_n1' => $row['keterangan_n1'] ?? null,
                    'keterangan_n' => $row['keterangan_n'] ?? null,
                ]
            );
        }

        return redirect()->route('pegawai.kelola-saldo')->with('success', 'Saldo cuti berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai)
    {
        $pegawai->delete();

        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil dihapus.');
    }

    // ==========================================
    // KELOLA AKUN PEGAWAI
    // ==========================================

    public function akunIndex(Request $request)
    {
        $pegawais = Pegawai::with('user')
            ->when($request->q, function ($query, $q) {
                $query->where('nama', 'like', "%{$q}%")
                    ->orWhere('nip', 'like', "%{$q}%");
            })
            ->orderBy('nama')
            ->paginate(20)
            ->withQueryString();

        return view('pegawai.kelola_akun', compact('pegawais'));
    }

    public function akunStore(Request $request, Pegawai $pegawai)
    {
        if ($pegawai->user) {
            return redirect()->route('pegawai.kelola-akun')->with('error', $pegawai->nama . ' sudah memiliki akun.');
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'min:3', 'confirmed'],
            'role' => ['required', 'string', 'in:pegawai,admin,atasan_langsung,kasubag,sekretaris,kepala_dinas,sekda,walikota'],
        ]);

        User::create([
            'nama' => $pegawai->nama,
            'nip' => $pegawai->nip,
            'password' => $data['password'],
            'role' => $data['role'],
        ]);

        return redirect()->route('pegawai.kelola-akun')->with('success', 'Akun berhasil dibuat untuk ' . $pegawai->nama . '.');
    }

    public function akunUpdate(Request $request, Pegawai $pegawai)
    {
        if (! $pegawai->user) {
            return redirect()->route('pegawai.kelola-akun')->with('error', $pegawai->nama . ' belum memiliki akun.');
        }

        $data = $request->validate([
            'role' => ['required', 'string', 'in:pegawai,admin,atasan_langsung,kasubag,sekretaris,kepala_dinas,sekda,walikota'],
            'password' => ['nullable', 'string', 'min:3'],
        ]);

        $updateData = ['role' => $data['role']];

        if (! empty($data['password'])) {
            $updateData['password'] = $data['password'];
        }

        $pegawai->user->update($updateData);

        return redirect()->route('pegawai.kelola-akun')->with('success', 'Akun ' . $pegawai->nama . ' berhasil diperbarui.');
    }

    public function akunDestroy(Pegawai $pegawai)
    {
        if (! $pegawai->user) {
            return redirect()->route('pegawai.kelola-akun')->with('error', $pegawai->nama . ' belum memiliki akun.');
        }

        $nama = $pegawai->user->nama;
        $pegawai->user->delete();

        return redirect()->route('pegawai.kelola-akun')->with('success', 'Akun ' . $nama . ' berhasil dihapus.');
    }
}
