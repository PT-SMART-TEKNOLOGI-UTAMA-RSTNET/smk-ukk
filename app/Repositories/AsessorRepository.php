<?php
/**
 * Created by PhpStorm.
 * User: reyang
 * Date: 2/20/2022
 * Time: 12:37 PM
 */

namespace App\Repositories;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class AsessorRepository
{
    public function create(Request $request) {
        try {
            $asessors = new User();
            $asessors->id = Uuid::uuid4()->toString();
            $asessors->name = $request->nama_asessor;
            $asessors->email = $request->email_asessor;
            $asessors->user_type = 'guru';
            $asessors->penguji_type = $request->jenis_asessor['value'];
            $asessors->password = Hash::make($request->kata_sandi);
            $asessors->saveOrFail();
            return $this->table(new Request(['id' => $asessors->id]));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function update(Request $request) {
        try {
            $asessors = User::where('id', $request->id)->first();
            $asessors->name = $request->nama_asessor;
            $asessors->email = $request->email_asessor;
            $asessors->penguji_type = $request->jenis_asessor['value'];
            if (strlen($request->kata_sandi) > 0) $asessors->password = Hash::make($request->kata_sandi);
            $asessors->saveOrFail();
            return $this->table(new Request(['id' => $asessors->id]));
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function delete(Request $request) {
        try {
            $asessors = User::where('id', $request->id)->first();
            $response = $this->table(new Request(['id' => $asessors->id]));
            $asessors->delete();
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
    public function table(Request $request) {
        try {
            $response = collect([]);
            $asessors = User::where('user_type','guru')->whereNotNull('penguji_type')->orderBy('name','asc');
            if (strlen($request->id) > 0) $asessors = $asessors->where('id', $request->id);
            if (strlen($request->type) > 0) $asessors = $asessors->where('penguji_type', $request->type);
            $asessors = $asessors->get();
            foreach ($asessors as $asessor){
                $response->push([
                    'value' => $asessor->id,
                    'label' => $asessor->name,
                    'meta' => [
                        'avatar' => (new \Laravolt\Avatar\Avatar)->create($asessor->name)->setTheme('colorful')->toBase64(),
                        'email' => $asessor->email,
                        'penguji' => [
                            'type' => $asessor->penguji_type,
                            'internal' => $asessor->penguji_internal,
                            'external' => $asessor->penguji_external
                        ],
                    ]
                ]);
            }
            return $response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(),500);
        }
    }
}