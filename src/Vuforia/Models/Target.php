<?php

namespace Vuforia\Models;

use Vuforia\Vuforia;

/**
 * Class Target.
 *
 * @property string $id
 * @property string $status
 * @property bool $active_flag
 * @property string $name
 * @property int $width
 * @property int $tracking_rating
 * @property string $reco_rating
 */
class Target extends Model
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var bool
     */
    private $is_loaded;

    /**
     * Target constructor.
     *
     * @param $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Returns the model attribute. If not exist, load it.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if ($name === 'id') {
            return $this->id;
        }

        if (!$this->is_loaded) {
            Vuforia::instance()->targets->find($this);
        }

        return $this->attributes[$name];
    }

    /**
     * Set the model attributes.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        if ($name === 'id') {
            $this->id = $value;

            return;
        }

        $this->is_loaded = true;

        $this->attributes[$name] = $value;
    }

    /**
     * Updates the target.
     *
     * @param string $marker_path
     * @param string $name
     * @param int    $width
     * @param string $metadata
     * @param bool   $is_active
     *
     * @return bool
     */
    public function update($marker_path = null, $name = null, $width = null, $metadata = null, $is_active = null): bool
    {
        $data = array();

        if (!is_null($marker_path)) {
            $data['image'] = self::encode_marker($marker_path);
        }

        if (!is_null($name)) {
            $data['name'] = $name;
        }

        if (!is_null($width)) {
            $data['width'] = $width;
        }

        if (!is_null($metadata)) {
            $data['application_metadata'] = self::encode_metadata($metadata);
        }

        if (!is_null($is_active)) {
            $data['active_flag'] = $is_active;
        }

        return Vuforia::instance()->targets->update($this->id, $data);
    }

    /**
     * Change name of the target.
     *
     * @param string $name
     *
     * @return bool
     */
    public function changeName(string $name)
    {
        return $this->update(null, $name, null, null, null);
    }

    /**
     * Change marker of the target.
     *
     * @param string $marker_path
     *
     * @return bool
     */
    public function changeMarker(string $marker_path)
    {
        return $this->update($marker_path, null, null, null, null);
    }

    /**
     * Change metadata of the target.
     *
     * @param string $metadata
     *
     * @return bool
     */
    public function changeMetadata(string $metadata)
    {
        return $this->update(null, null, null, $metadata, null);
    }

    /**
     * Make target inactive.
     *
     * @return bool
     */
    public function makeInactive()
    {
        return $this->update(null, null, null, null, false);
    }

    /**
     * Make target active.
     *
     * @return bool
     */
    public function makeActive()
    {
        return $this->update(null, null, null, null, true);
    }

    /**
     * Change width of marker.
     *
     * @param int $width
     *
     * @return bool
     */
    public function changeWidth(int $width)
    {
        return $this->update(null, null, $width, null, null);
    }

    /**
     * Activates the target. Alias of makeActive.
     */
    public function activate()
    {
        $this->makeActive();
    }

    /**
     * Deletes the marker.
     *
     * @return bool
     */
    public function delete()
    {
        return Vuforia::instance()->targets->delete($this->id);
    }

    /**
     * Returns all targets.
     *
     * @return Target[]
     */
    public static function all()
    {
        return Vuforia::instance()->targets->all();
    }

    /**
     * Creates a new marker.
     *
     * @param string $marker_path
     * @param string $name
     * @param int    $width
     * @param string $metadata
     * @param bool   $is_active
     *
     * @return Target
     */
    public static function create(string $marker_path, string $name, int $width, string $metadata, bool $is_active): Target
    {
        return Vuforia::instance()->targets->create(array(
            'width' => $width,
            'name' => $name,
            'image' => self::encode_marker($marker_path),
            'application_metadata' => self::encode_metadata($metadata),
            'active_flag' => $is_active,
        ));
    }

    /**
     * Encodes the metadata.
     *
     * @param string $metadata
     *
     * @return string
     */
    private static function encode_metadata(string $metadata): string
    {
        return base64_encode($metadata);
    }

    /**
     * Encodes the marker.
     *
     * @param string $marker_path
     *
     * @return string
     */
    private static function encode_marker(string $marker_path): string
    {
        $file = file_get_contents($marker_path);
        if ($file) {
            return base64_encode($file);
        }

        return '';
    }
}
