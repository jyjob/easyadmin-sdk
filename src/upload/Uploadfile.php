<?php

// +----------------------------------------------------------------------
// | EasyAdmin
// +----------------------------------------------------------------------
// | PHP交流群: 763822524
// +----------------------------------------------------------------------
// | 开源协议  https://mit-license.org 
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zhongshaofa/EasyAdmin
// +----------------------------------------------------------------------

namespace EasyAdmin\upload;

use EasyAdmin\exception\upload\EmptyRootDirException;
use EasyAdmin\upload\driver\Alioss;
use EasyAdmin\upload\driver\Bos;
use EasyAdmin\upload\driver\Local;
use EasyAdmin\upload\driver\Qnoss;
use EasyAdmin\upload\driver\Txcos;
use think\File;

/**
 * 上传组件
 * Class Uploadfile
 * @package EasyAdmin\upload
 */
class Uploadfile
{

    /**
     * 当前实例对象
     * @var object
     */
    protected static $instance;

    /**
     * 上传方式
     * @var string
     */
    protected $uploadType = 'local';

    /**
     * 上传配置文件
     * @var array
     */
    protected $uploadConfig;

    /**
     * 需要上传的文件对象
     * @var File
     */
    protected $file;

    /**
     * 保存上传文件的数据表
     * @var string
     */
    protected $tableName = 'system_uploadfile';

    /**
     * 保存的其他参数
     * @var array
     */
    protected $saveExtra = [];

    /**
     * 服务器根目录
     * @var string
     */
    protected $root_dir;

    /**
     * 文件访问域名
     * @var string
     */
    protected $file_view_domain;

    /**
     * 文件访问相对路径
     * @var string
     */
    protected $file_view_path;

    /**
     * 文件保存相对路径
     * @var string
     */
    protected $save_file_path;

    /**
     * 文件保存磁盘
     * @var string
     */
    protected $save_file_disk;

    /**
     * 获取对象实例
     * @return Uploadfile|object
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 设置上传对象
     * @param $value
     * @return $this
     */
    public function setFile($value)
    {
        $this->file = $value;
        return $this;
    }

    /**
     * 设置上传文件
     * @param $value
     * @return $this
     */
    public function setUploadConfig($value)
    {
        $this->uploadConfig = $value;
        return $this;
    }

    /**
     * 设置上传方式
     * @param $value
     * @return $this
     */
    public function setUploadType($value)
    {
        $this->uploadType = $value;
        return $this;
    }

    /**
     * 设置保存数据表
     * @param $value
     * @return $this
     */
    public function setTableName($value)
    {
        $this->tableName = $value;
        return $this;
    }

    /**
     * 设置其他保存参数
     * @param array $extra
     * @return $this
     */
    public function setSaveExtra(array $extra = []): Uploadfile
    {
        $this->saveExtra = $extra;
        return $this;
    }

    /**
     * 服务器根目录
     * @param string $root_dir
     * @return $this
     */
    public function setRootDir(string $root_dir): Uploadfile
    {
        $this->root_dir = $root_dir;
        return $this;
    }

    /**
     * 文件访问域名
     * @param string $file_view_domain
     * @return $this
     */
    public function setFileViewDomain(string $file_view_domain): Uploadfile
    {
        $this->file_view_domain = $file_view_domain;
        return $this;
    }

    /**
     * 文件访问相对路径
     * @param string $file_view_path
     * @return $this
     */
    public function setFileViewPath(string $file_view_path): Uploadfile
    {
        $this->file_view_path = $file_view_path;
        return $this;
    }

    /**
     * 文件保存相对路径
     * @param string $save_file_path
     * @return $this
     */
    public function setSaveFilePath(string $save_file_path): Uploadfile
    {
        $this->save_file_path = $save_file_path;
        return $this;
    }

    /**
     * 文件保存磁盘
     * @param string $save_file_disk
     * @return $this
     */
    public function setSaveFileDisk(string $save_file_disk): Uploadfile
    {
        $this->save_file_disk = $save_file_disk;
        return $this;
    }

    /**
     * 获取服务器根目录
     * @return string
     * @throws EmptyRootDirException
     */
    public function getRootDir(): string
    {
        if ($this->uploadType != 'local' && empty($this->root_dir)) {
            throw new EmptyRootDirException('root dir is empty');
        }
        return $this->root_dir ?? '';
    }

    /**
     * 获取文件访问域名
     * @return string
     */
    public function getFileViewDomain(): string
    {
        return $this->file_view_domain ?? request()->domain();
    }

    /**
     * 获取文件访问相对路径
     * @return string
     */
    public function getFileViewPath(): string
    {
        return $this->file_view_path ?? '';
    }

    /**
     * 获取文件保存相对路径
     * @return string
     */
    public function getSaveFilePath(): string
    {
        return $this->save_file_path ?? 'attachment/upload';
    }

    /**
     * 获取文件保存磁盘
     * @return string
     */
    public function getSaveFileDisk(): string
    {
        return $this->save_file_disk ?? 'public';
    }

    /**
     * 保存文件
     * @return array|void
     */
    public function save()
    {
        $obj = null;
        if ($this->uploadType == 'local') {
            $obj = new Local();
        } elseif ($this->uploadType == 'alioss') {
            $obj = new Alioss();
        } elseif ($this->uploadType == 'qnoss') {
            $obj = new Qnoss();
        } elseif ($this->uploadType == 'txcos') {
            $obj = new Txcos();
        } elseif ($this->uploadType == 'bos') {
            $obj = new Bos();
        }
        // switch ($this->uploadType) {
        //     default:
        //         $saveObj
        //             ->setSaveFileDisk($this->getSaveFileDisk())
        //             ->setSaveFilePath($this->getSaveFilePath())
        //             ->setFileViewDomain($this->getFileViewDomain())
        //             ->setFileViewPath($this->getFileViewPath());
        //         break;
        // }
        $save = $obj->setUploadConfig($this->uploadConfig)
            ->setUploadType($this->uploadType)
            ->setTableName($this->tableName)
            ->setFile($this->file)
            ->setSaveExtra($this->saveExtra)
            ->setRootDir($this->getRootDir())
            ->setSaveFileDisk($this->getSaveFileDisk())
            ->setSaveFilePath($this->getSaveFilePath())
            ->setFileViewDomain($this->getFileViewDomain())
            ->setFileViewPath($this->getFileViewPath())
            ->save();
        return $save;
    }
}