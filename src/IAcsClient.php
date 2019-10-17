<?php

namespace CodeMonkeyLuffy\Aliyun\Core;

interface IAcsClient
{
    /**
     * @param AcsRequest $request
     *
     * @return mixed
     */
    public function doAction($request);
}
