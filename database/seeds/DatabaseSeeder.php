<?php

use App\File;
use App\Folder;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{


    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);

        DB::table('users')->insert(array(
                'id' => 1,
                'name' => 'admin',
                'last_name' => 'edm',
                'gender' => 'm',
                'email' => 'admin.edm@edm.com',
                'password' => bcrypt('secret'),
                'created_at' => now()->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s')
            )
        );

        $file_types = [
            'pdf',
            'png',
            'xlsx',
            'docxs'
        ];



        factory(Folder::class, 5)->create()->each(function (Folder $folder) {

            $faker = Factory::create();

            $subFolderQty = $faker->randomElement( [0,1,2,3]);

            if ($subFolderQty) {
                for($i = 0; $i <= $subFolderQty; $i ++) {
                    Folder::create([
                        'user_id' => 1,
                        'parent_id' => $folder->id,
                        'name' => $faker->name,
                        'is_folder' => true,
                    ]);
                }
            }
        });

        factory(File::class, 100)->create()->each(function (File $file) use ($file_types) {

            $faker = Factory::create();
            $foldersId =  Folder::all()->pluck('id')->toArray();

            $foldersId = array_merge([null], $foldersId);

            $file->update([
                'parent_id' => $faker->randomElement($foldersId),
                'type' => $faker->randomElement($file_types),
                'size' => $faker->randomNumber(),
            ]);
        });

    }
}
