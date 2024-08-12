<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;


class CategoriesController extends Controller
{
    use ImageTrait , ResponseTrait ;

    
   /**
     * todo return all categories.
     */
    public function index(Request $request){
        $cat = Categories::select('id','name','location','photo')->get();
        return $this->returnData("categories",$cat);
    }


    /**
     * todo return info of this categorie.
     */
    public function show(Request $request){
      $cat = Categories::find($request->catid);
      return $this->returnData("categories",$cat);
    }


   /**
     * todo return categories images.
     */
    public function imagescat(Request $request){
        if(isset($request->cat)){
          return $this->returnimagecat($request->cat);}
        else {return 'null';}
    }
}
