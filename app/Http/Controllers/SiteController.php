<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SiteController extends Controller
{
    public function index()
    {
        return "Proteus Zero Knowledge - PoC";
    }

    public function client()
    {
        return view('client', []);
    }

    public function admin()
    {
        return view('admin', []);
    }

    public function createDatarequest(Request $request)
    {
        try {
            $datarequest = json_decode($request->getContent(), 1) ?: [];
            $db = new \MicroDB\Database('../storage/microdb/datarequest');
            $id = $db->create($datarequest);
            return new JsonResponse((object)[
                'id' => $id,
            ], 200);
        } catch (\Exception $ex) {
            return new JsonResponse($ex->getMessage(), 500);
        }
    }

    public function getAllDatarequest()
    {
        try {
            $db = new \MicroDB\Database('../storage/microdb/datarequest');
            return new JsonResponse($db->find(function($datarequest) {
                return true;
            }), 200);
        } catch (\Exception $ex) {
            return new JsonResponse($ex->getMessage(), 500);
        }
    }

    public function getDatarequest($id)
    {
        try {
            $db = new \MicroDB\Database('../storage/microdb/datarequest');
            return new JsonResponse((object)$db->load($id), 200);
        } catch (\Exception $ex) {
            return new JsonResponse($ex->getMessage(), 500);
        }
    }

    public function updateDatarequest($id, Request $request)
    {
        try {
            $update = json_decode($request->getContent(), 1) ?: [];
            $db = new \MicroDB\Database('../storage/microdb/datarequest');
            $datarequest = $db->load($id);
            $datarequest['adminMessage'] = $update['adminMessage'];
            $db->save($id, $datarequest);
            return new JsonResponse((object)$datarequest, 200);
        } catch (\Exception $ex) {
            return new JsonResponse($ex->getMessage(), 500);
        }
    }

    public function createAdmin(Request $request)
    {
        try {
            $admin = json_decode($request->getContent(), 1) ?: [];
            $db = new \MicroDB\Database('../storage/microdb/admin');
            $id = $db->create($admin);
            return new JsonResponse((object)[
                'id' => $id,
            ], 200);
        } catch (\Exception $ex) {
            return new JsonResponse($ex->getMessage(), 500);
        }
    }

    public function getAllAdmin()
    {
        try {
            $db = new \MicroDB\Database('../storage/microdb/admin');
            return new JsonResponse($db->find(function($admin) {
                return true;
            }), 200);
        } catch (\Exception $ex) {
            return new JsonResponse($ex->getMessage(), 500);
        }
    }

    public function getAdmin($id)
    {
        try {
            $db = new \MicroDB\Database('../storage/microdb/admin');
            return new JsonResponse((object)$db->load($id), 200);
        } catch (\Exception $ex) {
            return new JsonResponse($ex->getMessage(), 500);
        }
    }
}
