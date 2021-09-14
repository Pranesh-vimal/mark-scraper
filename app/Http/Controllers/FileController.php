<?php

namespace App\Http\Controllers;

use App\Exports\ImportFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Goutte\Client;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $client = new Client();

        $request->validate([
            'file' => 'required',
        ]);

        $array = Excel::toArray(new ImportFile, $request->file);

        $response = Http::asForm()->post('https://results.kongu.edu/xxiofrego.php', [
            'regno' => '17isr031', 'dob' => '19.03.2000',
        ]);
        $dom = new \DOMDocument(); 
        $html= $response->body();
        $html=preg_replace('/<!--(.|\s)*?--!>/', '', $html);
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_use_internal_errors(false);
        
        dd($dom);
    }
    
    
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBody($rollno, $dob)
    {
        $response = Http::asForm()->post('https://results.kongu.edu/xxiofrego.php', [
            'regno' => $rollno, 'dob' => $dob,
        ]);

        return (($response->body()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
