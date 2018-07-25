<?php

declare(strict_types=1);

namespace Shopworks\Tests\Integration;

use Faker\Factory;
use Mockery;
use OndraM\CiDetector\Ci\CiInterface;
use OndraM\CiDetector\CiDetector;
use Shopworks\Git\Review\VersionControl\GitBranch;
use Shopworks\Tests\GitTestCase;
use StaticReview\File\File;

class GitBranchTest extends GitTestCase
{
    /** @var GitBranch $gitBranch */
    private $gitBranch;
    private $topicBranchName;

    public function setUp(): void
    {
        parent::setUp();

        $this->topicBranchName = (Factory::create())->word;

        $command = <<<EOT
touch master-file-a.txt && /usr/bin/git add master-file-a.txt && /usr/bin/git commit -m "master commit a" &&
/usr/bin/git checkout -b {$this->topicBranchName} && touch test-branch-file-a.txt &&
/usr/bin/git add test-branch-file-a.txt &&
/usr/bin/git commit -m "test branch commit a" && touch test-branch-file-b.txt &&
/usr/bin/git add test-branch-file-b.txt && /usr/bin/git commit -m "test branch commit b" &&
/usr/bin/git checkout master && touch master-file-b.txt && /usr/bin/git add . &&
/usr/bin/git commit -m "master commit b"
EOT;

        $this->runProcess($command);
        $this->gitBranch = new GitBranch(
            Mockery::mock(CiDetector::class, [
                'isCiDetected' => false,
            ]),
            $this->directory
        );

        $this->assertEquals('master', $this->gitBranch->getName());
        $this->assertFalse($this->gitBranch->isDirty());
    }

    /**
     * @test
     */
    public function it_can_retrieve_branch_name(): void
    {
        $branchName = $this->gitBranch->getName();

        $this->assertEquals('master', $branchName);

        $this->checkoutBranch($this->topicBranchName);

        $branchName = $this->gitBranch->getName();

        $this->assertEquals($this->topicBranchName, $branchName);
    }

    /** @test */
    public function it_gets_the_branch_name_from_continuous_integration(): void
    {
        $gitBranch = new GitBranch(
            Mockery::mock(CiDetector::class, [
                'isCiDetected' => true,
                'detect' => Mockery::mock(CiInterface::class, [
                    'getGitBranch' => 'example-branch',
                ]),
            ]),
            $this->directory
        );

        $this->assertEquals('example-branch', $gitBranch->getName());
    }

    /**
     * @test
     */
    public function it_can_see_if_branch_is_dirty(): void
    {
        $this->runProcess('touch modified-file.txt');

        $this->assertTrue($this->gitBranch->isDirty());

        $this->runProcess('rm modified-file.txt');

        $this->assertFalse($this->gitBranch->isDirty());
    }

    /**
     * @test
     */
    public function it_can_get_parent_hash_at_pointer_to_master(): void
    {
        $masterCommitId = \trim($this->runProcess("/usr/bin/git log --grep='master commit a' --format='%H'")->getOutput());

        $this->checkoutBranch($this->topicBranchName);

        $this->assertEquals($masterCommitId, $this->gitBranch->getParentHash());
    }

    /**
     * @test
     */
    public function it_can_get_all_changed_files_on_branch_including_uncommitted(): void
    {
        $this->checkoutBranch($this->topicBranchName);

        $changedFiles = $this->gitBranch->getChangedFiles();

        $this->assertCount(2, $changedFiles);

        $this->assertTrue($changedFiles->contains(function (File $file) {
            return $file->getName() === 'test-branch-file-a.txt' && $file->getExtension() === 'txt';
        }));

        $this->assertTrue($changedFiles->contains(function (File $file) {
            return $file->getName() === 'test-branch-file-b.txt' && $file->getExtension() === 'txt';
        }));
    }

    /** @test */
    public function it_gets_the_correct_parent_hash_for_a_branch_using_merge_commit_strategy_on_the_master_branch(): void
    {
        $this->runProcess('/usr/bin/git checkout -b feature-commits-collection');
        $branchName = $this->gitBranch->getName();
        $this->assertEquals('feature-commits-collection', $branchName);
        $command = <<<'EOT'
touch feature-commits-file-a.txt && /usr/bin/git add . && /usr/bin/git commit -m "commit subject a" \ 
-m "Some body message also" && git checkout master && /usr/bin/git merge feature-commits-collection
EOT;
        $this->runProcess($command);
        $masterHash = \trim($this->runProcess('/usr/bin/git rev-parse --verify HEAD')->getOutput());
        $command = <<<'EOT'
/usr/bin/git checkout -b second-branch && touch feature-commits-file-b.txt && /usr/bin/git add . && 
/usr/bin/git commit -m "commit subject b"
EOT;
        $this->runProcess($command);
        $this->assertEquals($masterHash, $this->gitBranch->getParentHash());
    }

    /**
     * @test
     */
    public function it_gets_the_correct_parent_hash_for_a_branch_using_rebase_strategy_on_the_master_branch(): void
    {
        $this->runProcess('/usr/bin/git checkout -b feature-commits-collection');
        $this->runProcess('/usr/bin/git checkout master && git commit -m "Empty commit" --allow-empty');
        $masterHash = \trim($this->runProcess('/usr/bin/git rev-parse --verify HEAD')->getOutput());
        $this->runProcess('/usr/bin/git checkout feature-commits-collection && git rebase master');
        $branchName = $this->gitBranch->getName();
        $this->assertEquals('feature-commits-collection', $branchName);

        $this->assertEquals($masterHash, $this->gitBranch->getParentHash());
    }

    /**
     * @test
     */
    public function it_can_determine_when_a_topic_branch_is_empty(): void
    {
        $this->runProcess('/usr/bin/git checkout -b feature-commits-collection');
        $this->runProcess('/usr/bin/git checkout master && git commit -m "Empty commit" --allow-empty');
        $masterHash = \trim($this->runProcess('/usr/bin/git rev-parse --verify HEAD')->getOutput());

        $this->runProcess('/usr/bin/git checkout feature-commits-collection && git rebase master');

        $branchName = $this->gitBranch->getName();
        $this->assertEquals('feature-commits-collection', $branchName);

        $this->assertTrue($this->gitBranch->isEmpty());
        $this->assertEquals($masterHash, $this->gitBranch->getParentHash());
    }

    private function checkoutBranch($branchName): void
    {
        $this->runProcess("/usr/bin/git checkout ${branchName}");
    }
}
