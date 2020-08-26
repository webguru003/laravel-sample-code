<?php

namespace App\Http\Controllers\invoicecontroller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Task;
use App\Client;
use App\User;
use App\Invoice;
use App\Setting;
use Illuminate\Support\Facades\Redirect;
class InvoiceController extends Controller
{
 
    public function index()
    {
     	$invoices=Invoice::all();
	 	return view('invoices.invoices',compact('invoices'));
    }

  
    public function create()
    {
      return view('invoices.create');
    }

    
    public function store(Request $request)
    {
		 $this->validate($request, [
                'task_id' => 'required',
                'date' => 'required|date',
                'kilometers' => 'required|numeric',
                'task_price' => 'required|numeric',
                'invoice_number' => 'required|unique:invoices',
				
                'vat' => 'required|numeric',
           
                    ],['invoice_number.unique' => 'Ya asignado a Factura','task_id.required' => 'Crear Expediente',]);
			
			$datevalue = explode("-",$request->date);
			$date = $datevalue[2]."-".$datevalue[1]."-".$datevalue[0];

			$Task = Task::find($request->task_id);
			$Task->kilometers=$request->kilometers;
			$Task->task_price = $request->task_price;
			$Task->status="facturada";
			$Task->completion_date=$date;
			

			
			$Setting = Setting::first();
			$Invoice = new Invoice;
			$Invoice->task_id = $request->task_id;
			$Invoice->invoice_number=$request->invoice_number;
			$Invoice->date_created = $date;
			$Invoice->fixed_kilometers  = $Setting->fixed_kilometers;
			$Invoice->kiometers_price  = $Setting->kiometers_price;
            $Invoice->vat  = $Setting->vat;
			
			if($request->kilometers > $Setting->fixed_kilometers)
			{
				$requied_kilometers=$request->kilometers-$Setting->fixed_kilometers;
			}
			else
			{
				$requied_kilometers=0;
			}
		$total_amount_withoutvat=$request->task_price+($requied_kilometers)*($Setting->kiometers_price);
		$vat=(($request->vat)/100)*$total_amount_withoutvat;
		$total_amount=$total_amount_withoutvat+$vat;
			
			$Invoice->total_amount  = $total_amount;	
			
			$Task->save();
			$Invoice->save();
			return Redirect::to('/invoices');
			
    }

 
    public function show($id)
    {
       // 
    }

  
    public function edit($id)
    {
       $invoice = Invoice::find($id);
	   $taskdata = Task::find($invoice->task_id);
	   $Setting = Setting::first();
	   return view('invoices.edit',compact('invoice','taskdata','setting'));
    }

 
    public function update(Request $request, $id)
    {
	    $this->validate($request, [
                'date' => 'required|date',
                'kilometers' => 'required|numeric',
                'task_price' => 'required|numeric',
                'invoice_number' => 'required',
				/*'numberofimages' => 'required|numeric',*/
                'vat' => 'required|numeric',
           
                    ]);
	   
		$datevalue = explode("-",$request->date);
		$date = $datevalue[2]."-".$datevalue[1]."-".$datevalue[0];
	   	$Invoice = Invoice::find($id);
	    $Task = Task::find($Invoice->task_id);

	   
			$Task->kilometers=$request->kilometers;
			$Task->task_price = $request->task_price;
			$Task->status="facturada";
			$Task->completion_date=$date;
			
			
			$Invoice->date_created = $date;
			$Invoice->invoice_number=$request->invoice_number;
			if($request->kilometers > $Invoice->fixed_kilometers)
			{
				$requied_kilometers=$request->kilometers-$Invoice->fixed_kilometers;
			}
			else
			{
				$requied_kilometers=0;
			}
			$total_amount_withoutvat=$request->task_price+($requied_kilometers)*($Invoice->kiometers_price);
			$vat=(($request->vat)/100)*$total_amount_withoutvat;
			$total_amount=$total_amount_withoutvat+$vat;
			$Invoice->total_amount  = $total_amount;	
			
			$Task->save();
			$Invoice->save();
			
			return Redirect::to('/invoices');
			
    }

	 public function print($id)
    {
        $invoice= Invoice::find($id);
		return view('invoices.print',compact('invoice'));
    }
	 public function pdf($id)
    {
        $invoice= Invoice::find($id);
		return view('invoices.pdf',compact('invoice'));
    }


    public function destroy($id)
    {
		 	$Invoice = Invoice::find($id);
			$Task = Task::find($Invoice->task_id);
			$Task->status="visitada";
			$Task->completion_date=null;
			$Task->save();
			$Invoice->delete();
        return Redirect::back();
	}

	public function taskdata(Request $request)
	{
		$taskdata = Task::find( $request->taskid);
		return $taskdata;

	}
}
