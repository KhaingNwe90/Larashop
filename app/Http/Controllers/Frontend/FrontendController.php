<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Http\Requests\SendEmailMessageRequest;
use Input;
use Mail;

/**
 * Class FrontendController
 * @package App\Http\Controllers
 */
class FrontendController extends Controller
{
	/**
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		//$categories = Category::where("parent_id", 0)->get();

		$categories = Category::nested()->get(); // for nested menu

		$categories_html = Category::renderAsHtml();

		$hot_products = Product::where("products.category_id", 13)->get();

		return view('frontend.index')->with(array("categories"=>$categories, "hot_products"=>$hot_products, "categories_html"=>$categories_html));
	}

	/**
	 * @return \Illuminate\View\View
	 */
	public function macros()
	{
		return view('frontend.macros');
	}

	public function products(){
		$categories = Category::where("parent_id", 0)->get();

		/*$hot_products = Product::join("categories", "categories.id", "=", "products.category_id")
		->where("products.category_id", 6)
		->select("products.*")->paginate(4);*/

		/*
		$fashion_products = Product::where("products.category_id", 1)->get();

		$sport_products = Product::where("products.category_id", 2)->get();

		$shirt_products = Product::where("products.category_id", 3)->get();

		$electronic_products = Product::where("products.category_id", 5)->get();

		$it_products = Product::where("products.category_id", 6)->get();

		$food_products = Product::where("products.category_id", 7)->get();

		$alcohol_products = Product::where("products.category_id", 8)->get();

		$jacket_products = Product::where("products.category_id", 9)->get();

		$test_image_products = Product::where("products.category_id", 12)->get();
		$hot_offer_products = Product::where("products.category_id", 13)->get();

		return view("frontend.products.index")->with(array("categories"=>$categories, "fashion_products"=>$fashion_products, "sport_products"=>$sport_products, "shirt_products"=>$shirt_products, "electronic_products"=>$electronic_products, "it_products"=>$it_products, "food_products"=>$food_products, "alcohol_products"=>$alcohol_products, "jacket_products"=>$jacket_products, "test_image_products"=>$test_image_products, "hot_offer_products"=>$hot_offer_products)); 
		*/

		/*
		$products = Product::join("categories", "categories.id", "=", "products.category_id")->paginate(16);

		return view("frontend.products.index")->with(array("products"=>$products, "categories"=>$categories));
		*/


		$category_id = Input::get("category");

		$category = null;
		$category = Category::find($category_id);

		/*if($category){
			$products = Product::where("category_id", $category_id)->paginate(16);
		}else{
			$products = Product::paginate(16);
		}*/

		$keyword = Input::get("q");

		//for default
		$products = Product::join("categories", "categories.id", "=", "products.category_id");

		// for clicking menu
		if($category){
			$products = $products->where("products.category_id", $category_id);
		}

		// for search
		if($keyword){
			$products = $products->where("products.title", "like", "%".$keyword."%");
		}

		$products = $products->orderBy("products.created_at", "Desc")
		->select("products.*", "categories.title AS category_title")
		->paginate(16);

		return view("frontend.products.index")
		->with(array("products"=>$products, "categories"=>$categories, "category"=>$category));
	}

	public function productdetails($id){
	   $categories = Category::where("parent_id", 0)->get();

        $product = Product::join("categories", "categories.id", "=", "products.category_id")
        ->join("brands", "brands.id", "=", "products.brand_id")
        ->where("products.id", $id)
        ->select("products.*", "categories.title AS category", "brands.name AS brand")
        ->first();

        if(!$product)
            abort(404);

        return view("frontend.products.details")->with(array("categories"=>$categories, "product"=>$product));
    }

    public function contactus(){
    	  $categories = Category::where("parent_id", 0)->get();

    	  return view("frontend.contactus")->with(array("categories"=>$categories));
    }

    public function send_message(SendEmailMessageRequest $request){
    	  $data = $request->all();

    	  //dd($data);

    	  Mail::send("frontend.email.contactmessage", $data, function($message){
    	  	$recepient = "white.nuzzle@gmail.com";
    	  	$message->to($recepient);
    	  	$message->subject("Message from LaraShop");
    	  });

    	  return redirect()->route('frontend.contactus')->withFlashSuccess("Your Message is sent.");
    }

    public function faq(){
    	  $categories = Category::where("parent_id", 0)->get();

    	  return view("frontend.faq")->with(array("categories"=>$categories));
    }
}
