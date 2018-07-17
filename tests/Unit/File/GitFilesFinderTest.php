<?php

declare(strict_types=1);

namespace Shopworks\Tests\Unit\File;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\MockInterface;
use Shopworks\Git\Review\File\CriteriaFormatter;
use Shopworks\Git\Review\File\GitFilesFinder;
use Shopworks\Git\Review\VersionControl\GitBranch;
use Shopworks\Tests\UnitTestCase;
use StaticReview\File\File;

class GitFilesFinderTest extends UnitTestCase
{
    /** @var MockInterface|GitBranch $gitBranch */
    private $gitBranch;
    /** @var MockInterface|Filesystem $gitBranch */
    private $fileSystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gitBranch = Mockery::mock(GitBranch::class);
        $this->fileSystem = Mockery::mock(Filesystem::class);
    }

    /**
     * @test
     */
    public function it_can_find_files_by_given_criteria(): void
    {
        $finder = new GitFilesFinder($this->gitBranch, new CriteriaFormatter(), $this->fileSystem);

        $this->gitBranch->shouldReceive('getChangedFiles')
            ->once()
            ->andReturn(new Collection([
                new File('A', 'a/b/c.txt', '/tmp/repo-base'),
                new File('A', 'c/b/a.txt', '/tmp/repo-base'),
            ]));

        $this->fileSystem->shouldReceive('exists')->times(1)->andReturn(true);

        $files = $finder->find(['a/b/c.txt']);

        $this->assertEquals(new Collection([
            new File('A', 'a/b/c.txt', '/tmp/repo-base'),
        ]), $files);
    }

    /**
     * @test
     */
    public function it_can_find_files_by_given_file_extension_criteria(): void
    {
        $finder = new GitFilesFinder($this->gitBranch, new CriteriaFormatter(), $this->fileSystem);

        $this->gitBranch->shouldReceive('getChangedFiles')
            ->once()
            ->andReturn(new Collection([
                new File('A', 'a/b/c.txt', '/tmp/repo-base'),
                new File('A', 'c/b/a.js', '/tmp/repo-base'),
            ]));

        $this->fileSystem->shouldReceive('exists')->times(2)->andReturn(true);

        $files = $finder->find(['/*'], ['js']);

        $this->assertEquals([
            'c/b/a.js',
        ], $files->map(function (File $file) {
            return $file->getRelativePath();
        })->toArray());
    }

    /**
     * @test
     */
    public function it_does_not_return_files_that_do_not_exist(): void
    {
        $finder = new GitFilesFinder($this->gitBranch, new CriteriaFormatter(), $this->fileSystem);

        $this->gitBranch->shouldReceive('getChangedFiles')
            ->once()
            ->andReturn(new Collection([
                new File('A', 'a/b/c.txt', '/tmp/repo-base'),
                new File('A', 'c/b/a.txt', '/tmp/repo-base'),
            ]));

        $this->fileSystem->shouldReceive('exists')->times(1)->andReturn(false);

        $files = $finder->find(['a/b/c.txt']);

        $this->assertEquals(new Collection([]), $files);
    }

    /**
     * @test
     */
    public function get_found_files_can_accommodate_wildcard_directory_search(): void
    {
        $finder = new GitFilesFinder($this->gitBranch, new CriteriaFormatter(), $this->fileSystem);

        $this->gitBranch->shouldReceive('getChangedFiles')
            ->once()
            ->andReturn(new Collection([
                new File('A', 'example/test1/subfolder/file.txt', '/tmp/repo-base'),
                new File('A', 'example/test2/subfolder/test.js', '/tmp/repo-base'),
                new File('A', 'example/test2/subfolder/anotherlevel/test.php', '/tmp/repo-base'),
                new File('A', 'example/test3/legacy/file.txt', '/tmp/repo-base'),
                new File('A', 'example/test2/file.txt', '/tmp/repo-base'),
                new File('A', 'example2/another-file.txt', '/tmp/repo-base'),
                new File('A', '.php_cs.dist', '/tmp/repo-base'),
                new File('A', '.php_cs', '/tmp/repo-base'),
            ]));

        $this->fileSystem->shouldReceive('exists')->times(6)->andReturn(true);

        $files = $finder->find([
            'example/*/subfolder',
            'example2/*',
            '.php_cs',
            '.php_cs.dist',
        ]);

        $this->assertEquals([
            '.php_cs',
            '.php_cs.dist',
            'example/test1/subfolder/file.txt',
            'example/test2/subfolder/anotherlevel/test.php',
            'example/test2/subfolder/test.js',
            'example2/another-file.txt',
        ], $files->map(function (File $file) {
            return $file->getRelativePath();
        })->toArray());
    }
}
