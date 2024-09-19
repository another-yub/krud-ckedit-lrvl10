<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() : View
    {
        //ambil data
        $mahasiswa = Mahasiswa::latest()->paginate(5);

        //return data
        return view('mahasiswa.index', compact('mahasiswa'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('mahasiswa.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) : RedirectResponse
    {
        // validasi form
        $validate = $request->validate([
            'image' => 'required|image|mimes:jpg,png,jpeg|max:5048',
            'nis' => 'required|min:5',
            'nama' => 'required|min:5',
            'jurusan' => 'required|min:5',
            'fakultas' => 'required|min:5',
            'konten' => 'required|min:5',
        ]);

        // upload image 
        $image = $request->file('image');
        $image->storeAs('public/mahasiswa', $image->hashName());

        // menambahkan ke database 
        Mahasiswa::create([
            'image' => $image->hashName(),
            'nis' => $request->nis,
            'nama' => $request->nama,
            'jurusan' => $request->jurusan,
            'fakultas' => $request->fakultas,
            'content' => $request->konten,
        ]);

        //return
        return redirect()->route('mahasiswa.index')->with(['success' => 'Data berhasil di tambahkan']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) : View
    {
        // ambil data dari id 
        $data = Mahasiswa::findOrFail($id);

        // return view 
        return view('mahasiswa.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) : View
    {
        // mengambil data
        $data = Mahasiswa::findOrFail($id);

        return view('mahasiswa.edit', compact('data'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) : RedirectResponse
    {
          // validasi form
          $validate = $request->validate([
            'image' => 'image|mimes:jpg,png,jpeg|max:5048',
            'nis' => 'required|min:5',
            'nama' => 'required|min:5',
            'jurusan' => 'required|min:5',
            'fakultas' => 'required|min:5',
            'konten' => 'required|min:5',
          ]);

          $data = Mahasiswa::findOrFail($id);

          if($request->hasFile('image'))
          {
            // upload new image 
            $image = $request->file('image');
            $image->storeAs('public/mahasiswa', $image->hashName());

            //delete old image
            Storage::delete('public/mahasiswa'.$data->image);

            // update with new image 
            $data->update([
                'image' => $image->hashName(),
                'nis' => $request->nis,
                'nama' => $request->nama,
                'jurusan' => $request->jurusan,
                'fakultas' => $request->fakultas,
                'content' => $request->konten,

            ]);

          } else {

            // update with out image 
            $data->update([
                'nis' => $request->nis,
                'nama' => $request->nama,
                'jurusan' => $request->jurusan,
                'fakultas' => $request->fakultas,
                'content' => $request->konten,
            ]);
          }

          //return
          return redirect()->route('mahasiswa.index')->with(['success' => 'Data berhasil di edit']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) : RedirectResponse
    {
        //ambil data
        $data = Mahasiswa::findOrFail($id);

        //delete image
        Storage::delete('public/mahasiswa'.$data->image);
        
        //delete
        $data->delete();

        //redirect
        return redirect()->route('mahasiswa.index')->with(['success' => 'Data berhasil di hapus']);
    }
}
