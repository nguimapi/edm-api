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
            'docx'
        ];

      /*  factory(Folder::class, 5)->create()->each(function (Folder $folder) {
            $qty = 25;
            $this->createSubFolders($folder, $qty);
        });

        factory(File::class, 1000)->create()->each(function (File $file) use ($file_types) {

            $faker = Factory::create();
            $foldersId =  Folder::all()->pluck('id')->toArray();

            $foldersId = array_merge([null], $foldersId);

            $file->update([
                'folder_id' => $faker->randomElement($foldersId),
                'type' => $extension = $faker->randomElement($file_types),
                'size' => $faker->randomNumber(),
                'name' => $file->name . '.'. $extension,
            ]);
        });*/

    }

    public function createSubFolders(Folder $folder, &$qty)
    {
        $faker = Factory::create();

        $subFolderQty = $faker->randomElement( [0,1,2,3]);

        for($i = 0; $i <= $subFolderQty; $i ++) {

            $subFolder = Folder::create([
                'user_id' => 1,
                'folder_id' => $folder->id,
                'name' => $faker->name,
                'is_folder' => true,
                'is_confirmed' => true,
            ]);
            $qty--;

            if ($qty > 0) {
                $this->createSubFolders($subFolder, $qty);
            }
        }

    }

}
