<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cooperative;
use App\Http\Requests\StoreCooperativeRequest;

class CooperativeController extends Controller
{
    public function store(StoreCooperativeRequest $request) {
        $cooperative = Cooperative::create([
            'user_id' => auth()->id(), 
            'name' => $request->name,
            'member_count' => $request->member_count,
            'status' => 'pending'
        ]);

        $cooperative->load('user:id,name,email');

        return $this->sendResponse($cooperative, 'ยื่นคำขอจัดตั้งสหกรณ์สำเร็จ', 201);
    }

    public function myRequests() {
        $cooperatives = Cooperative::with('user:id,name,email')
                                   ->where('user_id', auth()->id())
                                   ->get();

        return $this->sendResponse($cooperatives, 'ดึงข้อมูลรายการคำขอสำเร็จ', 200);
    }

    public function index(Request $request) {
        $query = Cooperative::with('user:id,name,email');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $cooperatives = $query->get();

        return $this->sendResponse($cooperatives, 'ดึงข้อมูลคำขอทั้งหมดสำเร็จ', 200);
    }

    public function review(Request $request, $id){
       $request->validate([
            'status' => 'required|in:approved,rejected', 
            'remark' => 'required|string'
        ], [
            'status.required' => 'กรุณาระบุสถานะ',
            'status.in' => 'สถานะต้องเป็น approved หรือ rejected เท่านั้น',
            'remark.required' => 'ต้องระบุเหตุผล/หมายเหตุประกอบการพิจารณาเสมอ'
        ]);

        $cooperative = Cooperative::find($id);

        if (!$cooperative) {
            return $this->sendError('ไม่พบคำขอที่ระบุ', null, 404);
        }

        if ($cooperative->status !== 'pending') {
            return $this->sendError('คำขอนี้ถูกพิจารณาไปแล้ว ไม่สามารถแก้ไขซ้ำได้', null, 400);
        }

        $cooperative->update([
            'status' => $request->status,
            'remark' => $request->remark
        ]);

        return $this->sendResponse($cooperative, 'พิจารณาคำขอสำเร็จ', 200);
    }
}
