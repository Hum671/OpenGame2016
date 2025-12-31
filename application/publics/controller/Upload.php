<?php

namespace app\publics\controller;

use think\Controller;
use app\model\AttachmentsModel;

Class Upload extends Base
{
    // 文件上传附件
    public function attach()
    {
        // 接收数据进行上传分类
        $type = request()->post('type');
        $path = './uploads/'.$type;

        $types = [
            ATTACH_TYPE_IMAGE,
            ATTACH_TYPE_VIDEO,
            ATTACH_TYPE_FILE
        ];
        if (!in_array($type, $types)) {
            $this->apiError('type只能选择'.implode(',', $types));
        }

        $file = request()->file('file');
        try {
            if ($type == ATTACH_TYPE_FILE) {
                $info = $file->move($path);
            } else {
                $info = $file->validate(['size'=>20*1024*1024, 'ext'=>'jpg,jpeg,png,gif'])->move($path);
            }
        } catch (\Throwable $e) {
            $this->apiError('上传失败，请重新选择!');
        }

        if ($info) {
            $json = '/uploads/'.$type.'/'.$info->getSaveName();
            if ($type == ATTACH_TYPE_FILE) {
                $arr = [
                    'path' => $json,
                    'name' => $file->getInfo('name'),
                ];
                $json = $arr;
            }
            $aModel = new AttachmentsModel;
            $aModel->uid = 0;
            $aModel->type = $type;
            $aModel->json = $type == ATTACH_TYPE_FILE ? json_encode($json) : $json;
            $aModel->save();

            return json([
                'code' => 200,
                'msg'  => '上传成功',
                'path' => $type == ATTACH_TYPE_FILE ? $json['path'] : $json,
                'name' => $file->getInfo('name'),
                'id'   => $aModel->id,
            ]);
        }

        return json([
            'code' => 0,
            'msg' => '上传失败,大小最大为20MB',
            'data' => $file->getError()
        ]);
    }
}
