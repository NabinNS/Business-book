<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function settingPage($id)
    {
        $user = User::find($id);
        return view('settings.setting', compact('user'));
    }
    public function updateUser($id, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'password' => 'required',
            'location' => 'required',
            'email' => 'required',
            'phone' => 'required',
        ]);
        $user = User::find($id);
        $user->update([
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'location' => $request->location,
            'email' => $request->email,
            'phone_number' => $request->phone,
        ]);
        return redirect('setting/' . $id)->with('success', 'User update successful');
    }
    public function updateCompany($id, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'vatno' => 'required',
            'address' => 'required',
            'phone' => 'required',
        ]);
        $userCompany = UserCompany::first();

        if ($userCompany) {
            // Update the existing user company record
            $logo = $request->logo;
            if ($request->hasFile('logo')) {
                $logo = time() . '-' . $request->name . '.' . $request->logo->extension();
                $request->logo->move(public_path('images'), $logo);
            }

            $userCompany->company_name = $request->name;
            $userCompany->address = $request->address;
            $userCompany->vat_no = $request->vatno;
            $userCompany->phone_number = $request->phone;
            $userCompany->logo_path = $logo;
            $userCompany->save();
        } else {
            // Create a new user company record
            $logo = $request->logo;
            if ($request->hasFile('logo')) {
                $logo = time() . '-' . $request->name . '.' . $request->logo->extension();
                $request->logo->move(public_path('images'), $logo);
            }

            $userCompany = new UserCompany;
            $userCompany->company_name = $request->name;
            $userCompany->address = $request->address;
            $userCompany->vat_no = $request->vatno;
            $userCompany->phone_number = $request->phone;
            $userCompany->logo_path = $logo;
            $userCompany->save();
        }

        return redirect('setting/' . $id)->with('success', 'User update successful');
    }
}