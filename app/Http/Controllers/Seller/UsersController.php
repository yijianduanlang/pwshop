<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    protected $modelName = 'User';
    protected $auth = 'users';
    protected $belongName = 'belong_id';
    protected $setUser = true;

    // 添加
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            // 判断是否存在相同得账号
            if ($this->getService($this->modelName, true)->where('username', $request->username)->exists()) return $this->formatError(__('tip.userNameExist'));
            $model = $this->getService($this->modelName, true)
                ->create([
                    'username'  => $request->username ?? '',
                    'password'  => Hash::make($request->password ?? '123456'),
                    'nickname'  => $request->nickname ?? 'Mysterious',
                    'avatar'    => $request->avatar ?? '',
                    $this->belongName => $this->getBelongId()
                ]);
            $model->roles()->sync($request->role_id ?? []);
            DB::commit();
            return $this->success([], __('tip.success'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }

    // 显示
    public function show($id)
    {
        $rs = $this->getService($this->modelName, true)->where($this->belongName, $this->getBelongId())->with(['roles'])->find($id);
        $role_id = [];
        $role_name = [];
        if (!empty($rs['roles'])) {
            foreach ($rs['roles'] as $v) {
                $role_id[] = $v['id'];
                $role_name[] = $v['name'];
            }
        }
        $rs['role_id'] = $role_id;
        $rs['role_name'] = $role_name;
        unset($rs['password']);
        return $this->success($rs, __('tip.success'));
    }

    // 修改
    public function update(Request $request, $id)
    {
        try {
            $belongName = $this->belongName;
            // 判断是否存在相同得账号
            if ($this->getService($this->modelName, true)->where('username', $request->username)->exists()) return $this->formatError(__('tip.userNameExist'));
            $model = $this->getService($this->modelName, true)->find($id);
            $model->username = $request->username;
            if (!empty($request->password)) {
                $model->password = Hash::make($request->password);
            }
            $model->nickname = $request->nickname ?? '';
            $model->avatar = $request->avatar ?? '';
            $model->$belongName = $this->getBelongId();
            $model->save();
            $model->roles()->sync($request->role_id ?? []);
            DB::commit();
            return $this->success([], __('tip.success'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }

    // 删除
    public function destroy($id)
    {
        $idArray = array_filter(explode(',', $id), function ($item) {
            return (is_numeric($item));
        });
        foreach ($idArray as $v) {
            $model = $this->getService($this->modelName, true)->find($v);
            $model->roles()->detach();
            $model->refresh();
        }
        $model->whereIn('id', $idArray)->where($this->belongName, $this->getBelongId())->delete();
        return $this->success([], __('tip.success'));
    }
}
