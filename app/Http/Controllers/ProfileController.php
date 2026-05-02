<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        return view('investor.profile.show');
    }
    
    public function update(Request $request)
    {
        // Logika update profile
    }
    
    public function updatePhoto(Request $request)
    {
        // Logika update foto profil
    }
}