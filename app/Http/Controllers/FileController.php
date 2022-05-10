<?php

namespace App\Http\Controllers;

use App\Exports\ExportFile;
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
        $heading = ['name', 'regno', 'gpa', 'cgpa'];
        foreach ($array[3] as $key => $value) {

            if ($key == 0) {
                continue;
            }

            if ($value[0] === null) {
                break;
            }

            $response = Http::asForm()->post('https://results.kongu.edu/xxiofy/xxiofyo.php', [
                'regno' => $value[0], 'dob' => $value[2],
            ]);

            $dom = new \DOMDocument();
            $html = $response->body();
            $html = preg_replace('/<!--(.|\s)*?--!>/', '', $html);
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_use_internal_errors(false);
            $dom->preserveWhiteSpace = false;
            $marks = [];
            $marks['name'] = $value[1];
            $marks['regno'] = $value[0];

            if ($dom->getElementsByTagName('table')->length > 0) {

                $tables = $dom->getElementsByTagName('table');
                $table = $tables->item(1);
                $rows = $table->getElementsByTagName('tr');

                $data = [];

                $data['regno'] = $value[0];
                $data['name'] = $value[1];
                $data['dob'] = $value[2];

                foreach ($rows as $index => $row) {

                    $info = $row->getElementsByTagName('td');
                    $grade = $row->getElementsByTagName('th');
                    if (isset($info->item(0)->nodeValue) && isset($info->item(1)->nodeValue) && isset($info->item(2)->nodeValue)) {
                        $data['marks'][$index]['sem'] = $info->item(0)->nodeValue;
                        $data['marks'][$index]['subject_code'] = $info->item(1)->nodeValue;
                        $data['marks'][$index]['subject_name'] = $info->item(2)->nodeValue;
                        if (!in_array($info->item(2)->nodeValue, $heading)) {
                            array_push($heading, $info->item(2)->nodeValue);
                        }
                    }

                    if (isset($grade->item(1)->nodeValue) && isset($grade->item(0)->nodeValue) && $index != 0) {
                        $data['marks'][$index]['credits'] = $grade->item(0)->nodeValue;
                        $data['marks'][$index]['grade'] = $grade->item(1)->nodeValue;
                    }
                }

                $cgpatable = $tables->item(2);
                $cgparow = $cgpatable->getElementsByTagName('tr');

                $col = $cgparow[0]->getElementsByTagName('th');
                $gpa = explode(':', $col->item(0)->nodeValue);
                $data['gpa'] = isset($gpa[1]) ? $gpa[1] : '-';
                $col = $cgparow[1]->getElementsByTagName('th');
                $cgpa = explode(':', $col->item(0)->nodeValue);
                $data['cgpa'] = isset($cgpa[1]) ? $cgpa[1] : '-';
                $marks['gpa'] = $data['gpa'];
                $marks['cgpa'] = $data['cgpa'];
                foreach ($data['marks'] as $key => $value) {
                    $marks[$value['subject_name']] = $value['grade'];
                }
            }
            $results[] = ($marks);
        }

        return Excel::download(new ExportFile($results, $heading), 'mark (2017).xlsx');
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
