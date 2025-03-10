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

class CustomerController extends Controller
{

// Customer Home Page

    public function login()
    {
        return view('customer.login');
    }

    public function check_data(Request $login)
    {
         // Validate the request (email and password should be provided)
         $login->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find the agent by email (assuming plain text passwords)
        $check_login = DB::table('customer')->where('c_email', $login->email)->first();

        // Check if agent exists
        if ($check_login != null) {
            // Directly compare the plain text password
            if ($login->password == $check_login->c_password) {
                // Password is correct, login the user
                session()->put('login', $check_login->c_email);
                session()->put('c_id',$check_login->c_id);
                return redirect()->route('c_home');
            } else {
                // Password doesn't match
                return back()->withErrors(['password' => 'Invalid credentials.']);
            }
        } else {
            // Agent not found
            return back()->withErrors(['email' => 'Email not found.']);
        }
    }

    public function home()
    {
        return view('customer.home');
    }

    public function registration()
    {
        return view('customer.registration');
    }

    public function stor_data(Request $data)
    {
        $insert = new Customer;
        $insert->c_username=$data->username;
        $insert->c_email=$data->email;
        $insert->c_phone=$data->phone;
        $insert->c_password=$data->password;
        $insert->c_address=$data->address;

        if($insert->save())
        {
            return redirect()->route('c_login');
        }
        else{
            echo "Error In Inserting please Contct To Developer";
        }
    }



// Store,Edit,Remove shop section
    public function shop()
    {
        $data= product::get();
        // $banner= Banner::get();
        return view('customer.shop')->with('data',$data);
    }

    // public function c_product_banner()
    // {
    //     $banner= Banner::get();
    //     return view('customer.shop')->with('data',$banner);

    // }

    // public function contact()
    // {
    //     return view('customer.contact');
    // }

    // public function store_contact(Request $data)
    // {
    //     $insert = new Contact;
    //     $insert->fname=$data->fname;
    //     $insert->lname=$data->lname;
    //     $insert->email=$data->email;
    //     $insert->address=$data->address;

    //     if($insert->save())
    //     {
    //         return redirect()->route('contact');
    //     }
    //     else{
    //         echo "Error In Inserting please Contct To Developer";
    //     }
    // }

    public function cart()
    {
        $getcartproduct = DB::table('cart')
                        ->join('product','cart.p_id','=','product.id')
                        ->where('u_id',session('c_id'))->get();
        // dd($getcartproduct);
        $total = DB::table('cart')
               ->join('product','cart.p_id','=','product.id')
               ->where('u_id',session('c_id'))->sum('price');
        if($getcartproduct){
            return view('customer.cart',['prod'=>$getcartproduct,'total'=>$total]);
        }

    }

    public function checkout()
    {
        $getcartproduct = DB::table('cart')
                        ->join('product','cart.p_id','=','product.id')
                        ->where('u_id',session('c_id'))->get();
        $get = DB:: table('customer')
                        ->where('c_id',session('c_id'))
                        ->value('c_username');
        $em = DB:: table('customer')
                        ->where('c_id',session('c_id'))
                        ->value('c_email');
        $count = DB::table('cart')
                        ->join('product','cart.p_id','=','product.id')
                        ->where('u_id',session('c_id'))
                        ->sum('price');
                        // return $get;
                        // return $count;
        // return $get;
        if ($getcartproduct){
            return view('customer.checkout',['data'=>$getcartproduct,'name'=>$get,'email'=>$em,'total'=>$count]);
        }
    }


    public function makeorder(request $req){
        $date = now();
        // $price = 72000;
        $makeorder = DB::table('orders')
                            ->insert([
                                'c_id'=>session('c_id'),
                                'p_price'=> $req->c_total,
                                'order_date'=> $date,
                                'address'=>$req->c_address,
                                'city'=>$req->c_city,
                                'state'=>$req->c_state_country,
                                'pincode'=>$req->c_postal_zip,
                            ]);
        if($makeorder){
            $det=DB::table('orders')->where('c_id',session('c_id'))->where('order_date',$date)->value('id');
            // return (int) $det;
            $qty=1;
            $prod=DB::table('cart')
                        ->join('product','cart.p_id','=','product.id')
                        ->where('u_id',session('c_id'))
                        ->select('cart.*','product.price')
                        ->get();
                foreach ($prod as $key) {
                    DB::table('orders_items')
                        ->insert([
                            'o_id'=>$det,
                            'p_id'=>$key->p_id,
                            'quntity'=>$qty,
                            'price'=>$key->price,
                        ]);
                    DB::table('product')
                        ->where('id',$key->p_id)
                        ->decrement('stock',1);
                    DB::table('cart')
                        ->where('u_id',session('c_id'))
                        ->where('p_id',$key->p_id)
                        ->delete();
                }
            // return redirect()->route('invoice',['date' => $date]);
            // return redirect()->route('thankyou');
            return redirect()->route('invoice',['date'=>$date]);
        }
    }

    public function invoice(Request $req)
    {

        $final = DB::table('orders')
                    ->join('orders_items','orders.id','=','orders_items.o_id')
                    ->join('product','orders_items.p_id','=','product.id')
                    ->where('order_date','=',$req->date)
                    ->get();
                    $total = DB::table('orders')->where('order_date','=',$req->date)->value('p_price');
            if($final)
            {
                return view('customer.thankyou',['data'=>$final,'total'=>$total]);
            }
    }

    public function thankyou()
    {
        return view('customer.thankyou');
    }

    public function remove_cart($id)
    {
        $delete =  DB::table('cart')->where('id',$id)->first();

        if($delete->delete())
        {
            return redirect()->route('cart');
        }
        else{
            return redirect()->route('error');
        }
    }

    public function c_contact()
    {
        return view('customer.contact');

    }


    public function c_store_contact(Request $data)
    {
        $insert = new Contact;
        $insert->fname=$data->fname;
        $insert->lname=$data->lname;
        $insert->email=$data->email;
        $insert->address=$data->address;
        $insert->message=$data->message;

        if($insert->save())
        {
            return redirect()->route('c_contact');
        }
        else{
            echo "Error In Inserting please Contct To Developer";
        }
    }

    public function addtocart(string $id){
        // return $id;
        $checkif = DB::table('cart')->where('p_id',$id)->where('u_id',session('c_id'))->count();
            // return $checkif;
            if($checkif){
                return "<script>
                alert('Alredy added..');
                </script>".redirect()->route('shop');
            }
            else{
                $qty = 1;
                $add = DB::table('cart')
                ->insert([
                    'u_id'=> session('c_id'),
                    'p_id'=> $id,
                    'qty' => $qty,
                ]);
                if($add){
                    return "<script>
                    alert('Added..');
                    </script>".redirect()->route('shop');

                }
            }
    }





}
