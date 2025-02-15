<?php
declare(strict_types=1);

namespace EasyAdmin\upload\driver;

use EasyAdmin\upload\FileBase;
use EasyAdmin\upload\trigger\SaveDb;

class Bos extends FileBase
{
    /**
     * 重写上传方法
     * @return array|void
     */
    public function save()
    {
        parent::saveFile();
        $upload = \EasyAdmin\upload\driver\bdoss\Bos::instance($this->uploadConfig)
            ->save($this->completeFilePath, $this->completeFilePath);
        if ($upload['save'] == true) {
            SaveDb::trigger($this->tableName, array_merge([
                'upload_type'   => $this->uploadType,
                'original_name' => $this->file->getOriginalName(),
                'mime_type'     => $this->file->getOriginalMime(),
                'file_ext'      => strtolower($this->file->getOriginalExtension()),
                'url'           => $upload['url'],
                'create_time'   => time(),
            ],$this->saveExtra));
        }
        $this->rmLocalSave();
        return $upload;
    }
}