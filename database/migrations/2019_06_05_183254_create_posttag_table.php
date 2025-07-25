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
 * @copyright  (c) 2019-2025 The South LaSalle Trading Corporation
 * @license    http://opensource.org/licenses/MIT
 * @author     Bob Bloom
 * @email      bob.bloom@lasallesoftware.ca
 * @link       https://lasallesoftware.ca
 * @link       https://packagist.org/packages/lasallesoftware/ls-blogbackend-pkg
 * @link       https://github.com/LaSalleSoftware/ls-blogbackend-pkg
 *
 */

// LaSalle Software
use Lasallesoftware\Librarybackend\Database\Migrations\BaseMigration;

// Laravel classes
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreatePosttagTable extends BaseMigration
{
    /**
     * The name of the database table
     *
     * @var string
     */
    protected $tableName = "post_tag";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ((!Schema::hasTable($this->tableName)) &&
            ($this->doTheMigration(env('APP_ENV'), env('LASALLE_APP_NAME')))) {

            Schema::create($this->tableName, function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');


                if ($this->getColumnType('posts', 'id') == "int") {
                    $table->integer('post_id')->unsigned()->index();
                } 
                if ($this->getColumnType('posts', 'id') == "biginit") {
                    $table->bigInteger('post_id')->unsigned()->index();
                }
                $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');


                if ($this->getColumnType('tags', 'id') == "int") {
                    $table->integer('tag_id')->unsigned()->index();
                } 
                if ($this->getColumnType('tags', 'id') == "biginit") {
                    $table->bigInteger('tag_id')->unsigned()->index();
                }
                $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
            });
        }
    }
}