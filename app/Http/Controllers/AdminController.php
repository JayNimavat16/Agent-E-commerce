<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Agent;
use App\Models\Product;
use App\Models\Banner;
use App\Models\Customer;
use App\Models\Orders;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{

// Login and Dashbord
    public function login()
    {
        return view('admin_login');

    }

    public function login_check(Request $login)
    {
        $login_check = DB::table('admin')->where('name', $login->name)->where('password',$login->password)->first();

        if($login_check!=null)
        {
            session()->put('login', $login_check->name);
            return redirect()->route('home');
        }
        else
        {
            return redirect()->route('login');
        }

    }

    public function Dashbord()
    {
        if(session()->has('login'))
        {

        $data= Product::get();
        $allorders = DB::table('orders')->count();
        // return view('admin_main')->with('data',$data,$allorders);
        return view('admin_main',['data'=>$data,'ord'=>$allorders]);
        dd ($allorders);
        }
        else
        {
            return redirect()->route('login');
        }
    }

    public function home()
    {
        if(session()->has('login'))
        {

        $data= Product::get();
        return view('home')->with('data',$data);
        }
        else
        {
            return redirect()->route('login');
        }
    }

    public function profile()
    {
        $data= Admin::get();
        return view('profile')->with('data',$data);
    }

    public function store_profile(Request $data)
    {
        $insert = new Admin;
        $insert->name=$data->username;
        $insert->email=$data->email;
        $insert->password=$data->password;

        if($insert->save())
        {
            return redirect()->route('login');
        }
        else{
            echo "Error In Inserting please Contct To Developer";
        }
    }

    public function update($id)
    {
        $update = Admin::where('id',$id)->first();
        return view('profile')->with('data',$update);

    }

    public function profile_update(Request $request)
{
    // Retrieve the admin record to update
    $update = Admin::where('id', $request->id)->first();

    // Check if an image was uploaded
    if ($request->hasFile('image')) {
        // Get the uploaded image file
        $image = $request->file('image');

        // Generate a unique filename based on the current time and original file extension
        $imageName = time() . 'Jay.' . $image->getClientOriginalExtension();

        // Move the image to a public directory (e.g., 'uploads/profile/')
        $image->move(public_path('admin/assets/image/'), $imageName);

        // Update the database with the new image path
        $update->image = 'admin/assets/image/' . $imageName;
    }

    // Save the updated record (only the image will be updated)
    if ($update->save()) {
        return redirect()->route('profile')->with('success', 'Profile image updated successfully.');
    } else {
        return redirect()->route('profile')->with('error', 'Error in updating, please contact the developer.');
    }
}


public function remove_profile($id)
{
    $admin = Admin::find($id);

    if ($admin) {
        // Delete the admin account
        $admin->delete();

        // Optionally, log the user out after account deletion
        // auth()->logout();

        return redirect()->route('login')->with('success', 'Your account has been deleted successfully.');
    } else {
        return redirect()->route('profile')->with('error', 'Account not found.');
    }
}


// Add,Remove Agent
    public function agent()
    {
        $data= Agent::get();
        return view('agent')->with('data',$data);

    }

    public function add_agent()
    {
        $data= Agent::get();
        return view('add_agent')->with('data',$data);

    }

    public function store_agent(Request $data)
    {
        $insert = new product;
        // dd($data->file('image'));
        $image = $data->file('a_image');
        $img_name = time()."-Jay.".$image->extension();
        // dd($img_name);
        if($image->move(public_path("Images"),$img_name))
        {
        $insert = new Agent;
        $insert->a_first_name=$data->first_name;
        $insert->a_last_name=$data->last_name;
        $insert->a_email=$data->email;
        $insert->a_phone=$data->phone;
        $insert->a_store_name=$data->store_name;
        $insert->a_store_info=$data->store_info;
        $insert->a_address=$data->address;
        $insert->a_password=$data->password;
        $insert->a_image=$img_name;
        if($insert->save())
            {
                return redirect()->route('agent');
            }
            else{
                echo "Error In Inserting please Contct To Developer";
            }
            // echo "Moved";
            // exit;
        }
        else
        {
            echo "Not Move";
        }



        if($insert->save())
        {
            return redirect()->route('add_agent');
        }
        else{
            return redirect()->back()->with('error', 'Not Created');
        }
    }

    // public function agent_update($id)
    // {

    //     $update = Agent::where('id',$id)->first();

    //     return view('agent_update')->with('data',$update);

    // }

    // public function update_end(Request $request)
    // {
    //     $update = agent::where('id',$request->id)->first();
    //     // dd($request->all())
    //     $update->name=$data->name;
    //     $update->email=$data->email;
    //     $update->password=$data->password;
    //     // $update->image=$data->image;

    //     if($update->save())
    //     {
    //         return redirect()->route('add_agent ');
    //     }
    //     else{
    //         echo "Error In Updating please Contct To Developer";
    //     }
    // }

    public function remove_agent($id)
    {
        $delete = agent::where('a_id',$id)->first();

        if($delete->delete())
        {
            return redirect()->route('agent');
        }
        else{
            echo "Error In Updating please Contct To Developer";
        }
    }


// Add,Remove Customer
    public function customer()
    {
        $data= Customer::get();
        return view('customer')->with('data',$data);

    }

    public function remove_customer($id)
    {
        $delete = customer::where('c_id',$id)->first();

        if($delete->delete())
        {
            return redirect()->route('customer');
        }
        else{
            echo "Error In Updating please Contct To Developer";
        }
    }


// Add,Remove Product
    public function product()
    {
        $data= product::get();
        return view('product')->with('data',$data);
    }

    public function approve_product($id)
    {
        // $product = product::find($id);
        // $product->status=1;
        // $product->save();
        // return redirect()->back();

        $product = product::find($id);
        $product->status='Approve';
        $product->save();
        return redirect()->back();
    }

    public function reject_product($id)
    {
        $product = product::find($id);
        $product->status='Rejected';
        $product->save();
        return redirect()->back();
    }

    public function remove_product($id)
    {
        $delete = product::where('id',$id)->first();

        if($delete->delete())
        {
            return redirect()->route('product');
        }
        else{
            echo "Error In Updating please Contct To Developer";
        }
    }

    public function contact_info()
    {
        $data= Contact::get();
        return view('contact_info')->with('data',$data);

    }

    public function remove_contact($id)
    {
        $delete = Contact::where('id',$id)->first();

        if($delete->delete())
        {
            return redirect()->route('contact_info');
        }
        else{
            echo "Error In Updating please Contct To Developer";
        }
    }

    public function product_banner()
    {
        $data= Banner::get();
        return view('product_banner')->with('data',$data);

    }

    public function store_product_banner(Request $data)
    {
        $insert = new Banner;
        // dd($data->file('image'));
        $image = $data->file('image');
        $img_name = time()."-Jay.".$image->extension();
        // dd($img_name);
        if($image->move(public_path("Images"),$img_name))
        {

            $insert->image=$img_name;


            if($insert->save())
            {
                return redirect()->route('product_banner');
            }
            else
            {
                return redirect()->route('error');
            }
        }
        else
        {
            return redirect()->route('error');
        }

    }

    public function remove_product_banner($id)
    {
        $delete = Banner::where('id',$id)->first();

        if($delete->delete())
        {
            return redirect()->route('product_banner');
        }
        else{
            echo "Error In Updating please Contct To Developer";
        }
    }

    public function error()
    {
        return view('error_page');

    }

}

