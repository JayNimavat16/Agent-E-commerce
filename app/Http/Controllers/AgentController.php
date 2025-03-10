<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Product;
use App\Models\Banner;
use App\Models\Customer;
use App\Models\Orders;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class AgentController extends Controller
{

// Login and Dashbord

public function agent_home()
{
    return view('agent.agent_home');

}

    public function agent_login()
    {
        return view('agent.agent_login');

    }

    public function check_login(Request $login)
    {
        // Validate the request (email and password should be provided)
        $login->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find the agent by email (assuming plain text passwords)
        $check_login = DB::table('agent')->where('a_email', $login->email)->first();

        // Check if agent exists
        if ($check_login != null) {
            // Directly compare the plain text password
            if ($login->password == $check_login->a_password) {
                // Password is correct, login the user
                session()->put('agent_login', $check_login->a_email);
                return redirect()->route('agent_dashbord');
            } else {
                // Password doesn't match
                return back()->withErrors(['password' => 'Invalid credentials.']);
            }
        } else {
            // Agent not found
            return back()->withErrors(['email' => 'Email not found.']);
        }
    }

    public function agent_dashbord(Request $data)
    {
        if(session()->has('agent_login'))
        {
        $data = Agent::where('a_id',$data->id)->first();
        return view('agent.agent_dashbord');
        }
        else
        {
            return redirect()->route('agent_login');
        }
    }

    public function agent_profile(Request $data)
    {
        if(session()->has('agent_login'))
        {
        $data = Agent::where('a_id',$data->id)->first();
        return view('agent.profile');
        }
        else
        {
            return redirect()->route('agent_login');
        }
    }
    // public function agent_contact()
    // {
    //     if(session()->has('agent_login'))
    //     {
    //         // $view =  ::get();
    //     return view('agent.contact');
    //     }
    //     else
    //     {
    //         return redirect()->route('agent_contact');
    //     }
    // }

    public function showProfile()
    {
        // Check if the agent is logged in by checking session
        $agentId = Session::get('a_id');  // Retrieve agent ID from the session

        if ($agentId) {
            // Fetch the agent's data from the database
            $agent = Agent::find($agentId);

            // If agent exists, return profile page with the agent's data
            return view('agent.profile', compact($agent));
        } else {
            // If not logged in, redirect to the login page
            return redirect()->route('agent_login')->with('error', 'Please log in to view your profile.');
        }
    }

    // Update agent profile
    public function updateProfile(Request $request)
    {
        // Validate input data
        $request->validate([
            'a_fist_name' => 'required|string|max:255',
            'a_last_name' => 'required|string|max:255',
            'a_phone' => 'required|string|max:15',
            'a_email' => 'required|email|max:255',
            'a_password' => 'nullable|string|min:6|confirmed',
            'a_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $agent = Agent::where('a_id', $request->a_id)->first();

        if ($agent) {
            // Update agent details
            $agent->a_fist_name = $request->a_fist_name;
            $agent->a_last_name = $request->a_last_name;
            $agent->a_phone = $request->a_phone;
            $agent->a_email = $request->a_email;

            if ($request->a_password) {
                $agent->a_password = Hash::make($request->a_password); // Update password if provided
            }

            // Handle image upload
            if ($request->hasFile('a_image')) {
                $image = $request->file('a_image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/agents'), $imageName);
                $agent->a_image = 'uploads/agents/' . $imageName;
            }

            // Save updated details
            if ($agent->save()) {
                return redirect()->route('agent.profile')->with('success', 'Profile updated successfully.');
            } else {
                return redirect()->route('agent.profile')->with('error', 'Error updating profile.');
            }
        } else {
            return redirect()->route('agent.profile')->with('error', 'Agent not found.');
        }
    }


    public function agent_contact()
    {
        return view('agent.contact');

    }


    public function store_contact(Request $data)
    {
        $insert = new Contact;
        $insert->fname=$data->fname;
        $insert->lname=$data->lname;
        $insert->email=$data->email;
        $insert->address=$data->address;
        $insert->message=$data->message;

        if($insert->save())
        {
            return redirect()->route('agent_contact');
        }
        else{
            echo "Error In Inserting please Contct To Developer";
        }
    }


// Store,Edit,Remove Product
    public function product()
    {
        $view = product::get();
        return view('agent.product')->with('data',$view);

    }

    public function add_product()
    {
        $data= product::get();
        return view('agent.add_product')->with('data',$data);

    }

    public function store_product(Request $data)
    {
        $insert = new product;
        // dd($data->file('image'));
        $image = $data->file('image');
        $img_name = time()."-Jay.".$image->extension();
        // dd($img_name);
        if($image->move(public_path("Images"),$img_name))
        {
            $insert->name=$data->name;
            $insert->des=$data->des;
            $insert->price=$data->price;
            $insert->stock=$data->stock;
            $insert->image=$img_name;


            if($insert->save())
            {
                return redirect()->route('add_product');
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

    }

    public function edit_product($id)
    {
        //echo $id;
        $update = product::where('id',$id)->first();

        return view('agent.update_end')->with('data',$update);

        //return view('agent.Product');
    }

    public function store_edit(Request $request)
    {
        $update = product::where('id',$request->id)->first();

        $update->p_name=$request->p_name;
        $update->info=$request->info;
        $update->image=$img_name;

        if($update->save())
        {
            return redirect()->route('add_product');
        }
        else{
            echo "Error In Updating please Contct To Developer";
        }
    }

    public function remove_product($id)
    {
        $delete = product::where('id',$id)->first();

        if($delete->delete())
        {
            return redirect()->route('a_product');
        }
        else{
            echo "Error In Updating please Contct To Developer";
        }
    }

    public function order()
    {
        $view = DB::table('orders')
        ->join('orders_items','orders.id','=','orders_items.o_id')
        ->join('product','orders_items.p_id','=','product.id')
        ->join('customer','orders.c_id','=','customer.c_id')
        ->get();
        // dd ($final);
        return view('agent.order')->with('data',$view);

    }

    public function remove_order($id)
    {
        $delete = Order::where('id',$id)->first();

        if($delete->delete())
        {
            return redirect()->route('a_product');
        }
        else{
            echo "Error In Updating please Contct To Developer";
        }
    }

}
