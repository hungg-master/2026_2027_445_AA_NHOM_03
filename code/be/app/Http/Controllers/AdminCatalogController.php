<?php

namespace App\Http\Controllers;

use App\Models\MonHoc;
use App\Models\PhongHoc;
use App\Services\SupportTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminCatalogController extends SupportController
{
    public function subjects()
    {
        return $this->ok(MonHoc::orderBy('id')->get());
    }

    public function rooms()
    {
        return $this->ok(PhongHoc::orderBy('id')->get());
    }

    public function storeSubject(Request $request)
    {
        return $this->save($request, MonHoc::class);
    }

    public function updateSubject(Request $request, int $id)
    {
        return $this->save($request, MonHoc::class, $id);
    }

    public function storeRoom(Request $request)
    {
        return $this->save($request, PhongHoc::class);
    }

    public function updateRoom(Request $request, int $id)
    {
        return $this->save($request, PhongHoc::class, $id);
    }

    public function deleteSubject(int $id)
    {
        return $this->remove(MonHoc::class, 'id_mon_hoc', $id);
    }

    public function deleteRoom(int $id)
    {
        return $this->remove(PhongHoc::class, 'id_phong_hoc', $id);
    }

    private function save(Request $request, string $model, ?int $id = null)
    {
        $rules = $model === MonHoc::class
            ? ['ten_mon_hoc' => 'required|string|max:255', 'mo_ta' => 'nullable|string|max:5000', 'lop' => 'nullable|string|max:100', 'tinh_trang' => 'sometimes|in:hoat_dong,tam_ngung']
            : ['so_phong' => 'required|string|max:100', 'dia_chi' => 'required|string|max:255', 'mo_ta' => 'nullable|string|max:5000'];
        $data = $this->input($request, $rules);
        $record = SupportTransaction::run(function () use ($model, $id, $data) {
            $record = $id ? $model::find($id) : new $model;
            if (! $record) {
                $this->reject('Không tìm thấy dữ liệu.', 404);
            }
            $record->fill($data)->save();

            return $record;
        });

        return $this->ok($record, 'Đã lưu dữ liệu.', $id ? 200 : 201);
    }

    private function remove(string $model, string $foreignKey, int $id)
    {
        SupportTransaction::run(function () use ($model, $foreignKey, $id) {
            $record = $model::find($id);
            if (! $record) {
                $this->reject('Không tìm thấy dữ liệu.', 404);
            }
            if (DB::table('lop_hocs')->where($foreignKey, $id)->exists()) {
                $this->reject('Dữ liệu đang được lớp học sử dụng.', 409);
            }
            if ($foreignKey === 'id_mon_hoc' && Schema::hasColumn('trial_bookings', 'id_mon_hoc')
                && DB::table('trial_bookings')->where('id_mon_hoc', $id)->exists()) {
                $this->reject('Môn học đang được yêu cầu học thử sử dụng.', 409);
            }
            $record->delete();
        });

        return $this->ok(null, 'Đã xóa dữ liệu.');
    }
}
