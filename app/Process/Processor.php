<?php

namespace Shopworks\Git\Review\Process;

class Processor
{
    public function process(Process $process, bool $realTime = false): Process
    {
        if ($realTime) {
            return $this->processRealTime($process);
        }

        $process->run();

        return $process;
    }

    private function processRealTime(Process $process): Process
    {
        $process->run(function ($type, $buffer): void {
            echo $buffer;
        });

        return $process;
    }
}
