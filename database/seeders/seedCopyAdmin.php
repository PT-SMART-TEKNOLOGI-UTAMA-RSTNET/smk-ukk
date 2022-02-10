<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class seedCopyAdmin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultUsers = DB::connection(config('database.erapor'))->table('users')->whereIn('level',['admin','guru','siswa'])->orderBy('level','asc')->get();
        if ($defaultUsers->count() > 0){
            $this->command->getOutput()->progressStart($defaultUsers->count());
            foreach ($defaultUsers as $user){
                $checkUser = User::where('email', $user->email)->limit(1)->get('id');
                if ($checkUser->count() === 0){
                    $newUser = new User();
                    $newUser->id = Uuid::uuid4()->toString();
                    $newUser->name = $user->name;
                    $newUser->email = $user->email;
                    $newUser->password = $user->password;
                    $newUser->user_type = $user->level;
                    if ($user->level == 'guru') {
                        $newUser->penguji_type = 'internal';
                    }
                    if ($user->level == 'siswa'){
                        $dataSiswa = DB::connection(config('database.erapor'))->table('siswas')->where('user', $user->id)->limit(1)->get();
                        if ($dataSiswa->count() > 0){
                            $dataSiswa = $dataSiswa->first();
                            $newUser->siswa = $dataSiswa->id;
                            $newUser->nis = $dataSiswa->nis;
                            $newUser->nisn = $dataSiswa->nisn;
                        }
                    }
                    $newUser->saveOrFail();
                }
                $this->command->getOutput()->progressAdvance();
            }
            $this->command->getOutput()->progressFinish();
        }
    }
}
