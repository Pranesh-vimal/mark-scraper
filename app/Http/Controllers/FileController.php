<?php

namespace App\Http\Controllers;

use App\Exports\ImportFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $request->validate([
            'file' => 'required',
        ]);

        $array = Excel::toArray(new ImportFile, $request->file);

        $results = [];

        foreach ($array[3] as $key => $value) {

            if ($key == 0) {
                continue;
            }

            $response = Http::asForm()->post('https://results.kongu.edu/xxiofrego.php', [
                'regno' => $value[0], 'dob' => $value[2],
            ]);

            $dom = new \DOMDocument();
            $html = $response->body();
            $html = preg_replace('/<!--(.|\s)*?--!>/', '', $html);
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_use_internal_errors(false);
            $dom->preserveWhiteSpace = false;
            if ($dom->getElementsByTagName('table')->length > 0) {
                $tables = $dom->getElementsByTagName('table');
                $table = $tables->item(1);
                $rows = $table->getElementsByTagName('tr');
                $data = [];

                $data['regno'] = $value[0];
                $data['name'] = $value[1];
                $data['dob'] = $value[2];

                foreach ($rows as $key => $row) {
                    $info = $row->getElementsByTagName('td');
                    $grade = $row->getElementsByTagName('th');
                    if (isset($info->item(0)->nodeValue) && isset($info->item(1)->nodeValue) && isset($info->item(2)->nodeValue)) {
                        $data[$key]['sem'] = $info->item(0)->nodeValue;
                        $data[$key]['subject_code'] = $info->item(1)->nodeValue;
                        $data[$key]['subject_name'] = $info->item(2)->nodeValue;
                    }
                    if (isset($grade->item(1)->nodeValue) && isset($grade->item(0)->nodeValue) && $key != 0) {
                        $data[$key]['credits'] = $grade->item(0)->nodeValue;
                        $data[$key]['grade'] = $grade->item(1)->nodeValue;
                    }
                }
            }
            $results[] = ($data);
        }
        dd($results);
        // foreach ($results as $key => $value) {
        //     foreach ($value as $k => $v) {
        //         if(gettype($v) == 'string'){

        //         }
        //     }
        //     dd($value);
        // }

        // header("Content-Disposition: attachment; filename=\"demo.xls\"");
        // header("Content-Type: application/vnd.ms-excel;");
        // header("Pragma: no-cache");
        // header("Expires: 0");
        // $out = fopen("php://output", 'w');
        // foreach ($results as $data) {
        //     fputcsv($out, $data, "\t");
        // }
        // fclose($out);
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
