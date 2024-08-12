<?php

namespace App\Http\Controllers;

use Auth;
use Exception;
use Validator;
use App\Models\Tickets;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Traits\Requests\TestAuth;
use App\Http\Controllers\Controller;
use App\Traits\validator\ValidatorTrait;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class TicketsController extends Controller
{
    use ResponseTrait , TestAuth , ValidatorTrait;
  


    /**
     * todo return all Tickets on this categories.
     */
    public function index(Request $request){
        $ticket = Tickets::where("cat_id",$request->catId)->with('categories')->get();
        return $this->returnData("Tickets",$ticket);
    }


   /**
     * todo Store a new Tickets in this list.
     */
    public function store(Request $request)
    {
        // ! valditaion
        $rules = $this->rulesStoreTicket();    
        $validator = $this->validate($request,$rules);
        if($validator !== true){return $validator;}
        
        // todo Add New Ticket //    
        $ticket = Tickets::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => strtolower($request ->status),
            'due_dates' => $request->due_dates,
            'cat_id' => $request->cat_id,
            'user_id' => auth()->user()->id,
        ]);

        if($ticket){return $this->returnSuccessMessage("Create Task Successfully .");}
        else{return $this->returnError('T001','Some Thing Wrong .');}
     }



    /**
     * todo Show the Tickets i want edit it.
     */
    public function edit(Request $request)
    {
      $ticket = Tickets::where("user_id",auth()->user()->id)->where("id",$request->ticketId)->get();
      return $this->returnData("Tickets",$ticket);

    }



    /**
     * todo Update the Tickets.
     */
    public function update(Request $request)
    {

      // ! valditaion
      $rules = $this->rulesUpdateticket();
      $validator = $this->validate($request,$rules);
      if($validator !== true){return $validator;}
      
      $ticket = Tickets::find($request->ticketId);
      $ticket->update([
            'title' => $request->title,
            'description' => $request->description,
            'status' => strtolower($request ->status),
            'due_dates' => $request->due_dates,
            'cat_id' => $request->cat_id,
          ]); 
      $msg = " Update Ticket : ".$ticket->title." successfully .";
      return $this->returnSuccessMessage($msg); 

    }



    /**
     * todo Remove the Tickets in trasheing.
     */
    public function destroy(Request $request)
    {

      // ! valditaion
      $rules = ["ticketId"=> "required|exists:Tickets,id",];
      $validator = $this->validate($request,$rules);
      if($validator !== true){return $validator;}

      // ? soft delete ticket //
      $ticket = Tickets::with(['user' => function ($query) {
      $query->select('id','name');  }]) //? Select only 'id' and 'name' columns from the users table
      ->find($request->ticketId);

      $msg = " delete ticket : ".$ticket->title . " , sir : " .$ticket->user->username . "  " ."successfully .";
      if($ticket){$ticket->delete();return $this->returnSuccessMessage($msg);}
      else{return $this->returnError("T404" , "This Tickets not fount");}

    }



    /**
     * todo Filtering the Tickets in this categories.
     */
    public function filter(Request $request)
    {
      $ticket = Tickets::where("user_id",auth()->user()->id)->where("status",strtolower($request->filter))->where("cat_id",$request->catId)->get();
      return $this->returnData("Tickets",$ticket);
    }


    /**
     * todo Filtering the Trash Tickets in all categories.
     */
    public function filterTrash(Request $request)
    {
      $ticket = Tickets::onlyTrashed()->where("user_id",auth()->user()->id)->where("status",strtolower($request->filter))->get();
      return $this->returnData("TicketsTrashed",$ticket);
    }



    /**
     * todo return all categories its trashed .
     */
    public function restoreindex()
    {
       $ticket = Tickets::where('user_id',auth()->user()->id)->onlyTrashed()->with('user','categories')->get();
       return $this->returnData("TicketsTrashed",$ticket);
    }



   /**
     * todo restore the Tickets on this categories.
     */
    public function restore(Request $request)
    {
       // ! valditaion
       $rules = ['ticketId' => 'required|exists:Tickets,id',];
       $validator = $this->validate($request,$rules);
       if($validator !== true){return $validator;}

       $ticket = Tickets::withTrashed()->find($request->ticketId);
       $this->checkTickets($ticket);  //? check its your take or not & found Tickets
       $ticket->restore();
       return $this->returnSuccessMessage("Restore Tickets Successfully .");
    }



    /**
     * todo Autocomplete Search the specified resource from storage.
     */
    public function autocolmpletesearch(Request $request)
    {
        // ! valditaion
        $rules = ["query" => "required" , "catid" => "required|exists:categories,id"];
        $validator = $this->validate($request,$rules);
        if($validator !== true){return $validator;}

        // ? search by title || description // 
        $query = $request->get('query');
        $filterResult = Tickets::where('user_id', auth()->user()->id)
            ->where("cat_id",$request->catid)
            ->where(function ($q) use ($query) {
            $q->where('title', 'LIKE', '%'.$query.'%')
            ->orWhere('description', 'LIKE', '%'.$query.'%');})
            ->get();
        return $this->returnData("Tickets",$filterResult);
    
    }
}
