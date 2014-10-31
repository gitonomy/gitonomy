<?php

/**
 * This file is part of Gitonomy.
 *
 * (c) Alexandre Salomé <alexandre.salome@gmail.com>
 * (c) Julien DIDIER <genzo.wm@gmail.com>
 *
 * This source file is subject to the GPL license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gitonomy\Bundle\JobBundle\Storage;

use Gitonomy\Bundle\JobBundle\Job\Job;

/**
 * The definition of a storage for jobs.
 */
interface StorageInterface
{
    /**
     * Stores a job in the the storage.
     *
     * @param string $name
     * @param mixed $parameters.
     *
     * @return int the ID
     */
    public function store($name, array $parameters);

    /**
     * Finds a processable job.
     *
     * @return [id, name, parameters] an array with 3 values (id, name and parameters)
     */
    public function find();

    /**
     * Returns the status of a job.
     *
     *     array(
     *         'running'       => true|false
     *         'fails'         => integer,
     *         'error_message' => string|null
     *     )
     *
     * @param mixed $id
     *
     * @return integer|string
     */
    public function getStatus($id);

    /**
     * Marks a job as finished, successful or not.
     */
    public function finish($id, $success, $message);
}
