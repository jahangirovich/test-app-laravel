<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tasks;
use Illuminate\Http\Request;
use Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!$request["developer"])
            return response(["status"=>"error","message"=> "Не передано имя разработчика"]);
        
        $sort_field = $request["sort_field"] ? $request["sort_field"] : "id";
        $sort_direction = $request["sort_direction"] ? $request["sort_direction"] : "asc";

        $tasks = Tasks::orderBy($sort_field,$sort_direction)->paginate(3);

        return response(["status"=>"ok", 
                            "message" => [
                                "tasks" => $tasks->items(),
                                "total_task_count" => $tasks->total(),
                                "current_page" => $tasks->currentPage()
                            ]
                        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $token = $request->bearerToken();
        
        $validator = Validator::make($data, [
            'username' => 'required|max:255',
            'email' => 'required|max:255',
            'text' => 'required|max:255',
        ]);

        if($validator->fails()){
            $failedRules = $validator->failed();

            if(isset($failedRules["username"])){
                return $this->handler("error", ["username"=>"Поле является обязательным для заполнения"],400);
            }
            if(isset($failedRules["email"])){
                return $this->handler("error", ["email"=>"Поле является обязательным для заполнения"],400);
            }
            return $this->handler("error", ["text"=>"Поле является обязательным для заполнения"], 400);
        }

        $ceo = Tasks::create($data);

        return response([ 'status' => "ok", 'message' => 'Created successfully'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tasks  $tasks
     * @return \Illuminate\Http\Response
     */
    public function show(Tasks $tasks)
    {
        //
    }

    public function handler($status,$message, $statusCode){
        return response(["status" => $status, "message" => $message],$statusCode);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tasks  $tasks
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $task = Tasks::find($id);
        $task["text"]   = $request["text"];
        $task["status"] = $request["status"];
        $task->save();

        return response(['status' => 'ok']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tasks  $tasks
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tasks $tasks)
    {
        //
    }
}
