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
        $dom->preserveWhiteSpace = false;
        $tables = $dom->getElementsByTagName('table');
        $table = $tables->item(1);
        $rows = $table->getElementsByTagName('tr');
        $data = [];
        foreach ($rows as $key => $row) {
            $info = $row->getElementsByTagName('td');
            $grade = $row->getElementsByTagName('th');
            if(isset($grade->item(1)->nodeValue) && isset($grade->item(0)->nodeValue) && $key!=0)
                $data[$key]['grade']= $grade->item(1)->nodeValue;
            if(isset($info->item(0)->nodeValue) && isset($info->item(1)->nodeValue) && isset($info->item(2)->nodeValue)){
                $data[$key]['sem']= $info->item(0)->nodeValue;
                $data[$key]['subject_code']= $info->item(1)->nodeValue;
                $data[$key]['subject_name']= $info->item(2)->nodeValue;            
            }
        }
        dd($data);
        
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
