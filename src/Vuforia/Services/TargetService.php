<?php

namespace Vuforia\Services;

use Vuforia\Models\Target;
use Vuforia\Vuforia;

/**
 * Class TargetService.
 */
class TargetService extends Service
{
    /**
     * Return all targets.
     *
     * @return Target[]
     */
    public function all()
    {
        $response = json_decode(Vuforia::instance()->request->get('targets')->getBody()->getContents());

        $targets = array();

        foreach ($response->results as $result) {
            $targets[] = new Target($result);
        }

        return $targets;
    }

    /**
     * Returns the target.
     *
     * @param Target $target
     *
     * @return Target
     */
    public function find(&$target)
    {
        if (!($target instanceof Target)) {
            $target = &Target::find($target);
        }

        $target_id = $target->id;

        $response = json_decode(Vuforia::instance()->request->get("targets/{$target_id}")->getBody()->getContents());

        $target->status = $response->status;
        $target->active_flag = $response->target_record->active_flag;
        $target->name = $response->target_record->name;
        $target->width = $response->target_record->width;
        $target->tracking_rating = $response->target_record->tracking_rating;
        $target->reco_rating = $response->target_record->reco_rating;

        return $target;
    }

    /**
     * Creates a new target and returns the data.
     *
     * @param array $data
     *
     * @return Target
     */
    public function create(array $data): Target
    {
        $response = json_decode(
            Vuforia::instance()
                ->request
                ->post('targets', json_encode($data))
                ->getBody()
                ->getContents()
        );

        return Target::find($response->target_id);
    }

    /**
     * Updates the existing data.
     *
     * @param string $target_id
     * @param array  $data
     *
     * @return bool
     */
    public function update(string $target_id, array $data): bool
    {
        return Vuforia::instance()
                ->request
                ->put("targets/{$target_id}", json_encode($data))
                ->getStatusCode() === 200;
    }

    /**
     * Deletes the target.
     *
     * @param string $target_id
     *
     * @return bool
     */
    public function delete(string $target_id)
    {
        return Vuforia::instance()
                ->request
                ->delete("targets/{$target_id}")
                ->getStatusCode() === 200;
    }

    /**
     * Retrieves duplicate targets
     *
     * @param string $target_id
     *
     * @return Target[]
     */
    public function duplicates(string $target_id)
    {
        $response = Vuforia::instance()
            ->request
            ->get("duplicates/{$target_id}")
            ->getBody()
            ->getContents();

        $response = json_decode($response);

        $similar_target_ids = $response->similar_targets;
        $similar_targets = array();

        foreach ($similar_target_ids as $similar_target_id) {
            $similar_targets[] = Target::find($similar_target_id);
        }

        return $similar_targets;
    }
}
