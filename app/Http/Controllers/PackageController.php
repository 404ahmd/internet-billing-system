<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

class PackageController extends Controller
{

     // function for get the 'add package' form
    public function index()
    {
        // variable for get all package
        // return the view package table
        $packages = Package::all();
        return view('package.package_management', compact('packages'));
    }

    // function for store new package to package table
    public function store(Request $request)
    {
        // validation on input package form
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'type' => 'required',
            'cycle' => 'required',
            'bandwidth' => 'required',
            'status' => 'required'
        ]);

        //check if package is existing
        $existing = Package::where('description', $request->description)->first();
        if ($existing) {
            //retun back with error message
            return redirect()->back()->with('error', 'paket sudah tersedia');
        }

        // ctreate new package adn store to database
        Package::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'type' => $request->type,
            'cycle' => $request->cycle,
            'bandwidth' => $request->bandwidth,
            'status' => $request->status
        ]);

        // return back if success and show the message
        return redirect()->back()->with('success', 'paket berhasil ditambahkan');
    }

    public function edit($id){
        $packages = Package::findOrFail($id);
        return view('package.package_edit', [
            'packages' => $packages,
        ]);
    }

    public function update(Request $request, $id){
        $validated = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'type' => 'required',
            'cycle' => 'required',
            'bandwidth' => 'required',
            'status' => 'required'
        ]);

        try {
            $pacakge = Package::findOrFail($id);
            $pacakge->update($validated);
            return redirect()->route('package.view')->with('success', 'paket berhasil diperbarui');

        } catch (\Exception $e) {
            return back()->with('error', 'gagal memperbarui paket' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id){
        $id_package = Package::findOrFail($id);

        if(!$id_package){
            return redirect()->back()->with('error', 'data gagal dihapus');
        }

        $id_package->delete();
        return redirect() -> route('package.view')->with('success', 'data berhasil dihapus');
    }
}
