<?php

/**
 * This file is part of the Lasalle Software blog back-end package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright  (c) 2019-2020 The South LaSalle Trading Corporation
 * @license    http://opensource.org/licenses/MIT
 * @author     Bob Bloom
 * @email      bob.bloom@lasallesoftware.ca
 * @link       https://lasallesoftware.ca
 * @link       https://packagist.org/packages/lasallesoftware/lsv2-blogbackend-pkg
 * @link       https://github.com/LaSalleSoftware/lsv2-blogbackend-pkg
 *
 */

namespace Lasallesoftware\Blogbackend\Commands;

// Laravel classes
use Illuminate\Console\Command;

class BlogcustomseedCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'lsblogbackend:blogcustomseed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the LaSalle Software blog custom database seeder.';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        //consoleOutput()->comment('Starting the custom blog database seeder...');
        $this->info('Starting the blog custom database seeder...');

        $this->call('db:seed', [
            '--class' => 'Lasallesoftware\\Blogbackend\\Database\\DatabaseSeeds\\DatabaseSeeder',
        ]);
    }
}
