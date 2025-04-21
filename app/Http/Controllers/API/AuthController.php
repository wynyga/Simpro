<?php

namespace App\Http\Controllers\API;
  
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{
 
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {

        $validator = FacadesValidator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
     
        if($validator->fails()){
            return $this->sendError('Nama/Email sudah digunakan.', $validator->errors());       
        }
     
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['user'] =  $user;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
  
  
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
  
        if (! $token = auth()->attempt($credentials)) {
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
  
        $success = $this->respondWithToken($token);
   
        return $this->sendResponse($success, 'User login successfully.');
    }
  
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        // $success = auth()->user();
        $user = auth()->user()->load('perumahan'); // Mengambil relasi perumahan
    
        $success = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'perumahan_id' => $user->perumahan_id,
            'nama_perumahan' => $user->perumahan ? $user->perumahan->nama_perumahan : null, // Cek apakah relasi ada
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
        return $this->sendResponse($success, 'User profile retrieved successfully.');
    }
  
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $user = auth()->user(); // Mendapatkan user yang terautentikasi
    
        if ($user) {
            $user->perumahan_id = null; // Mengatur perumahan_id kembali ke null
            $user->save(); // Menyimpan perubahan
        }
    
        auth()->logout(); // Invalidasi token
    
        return $this->sendResponse([], 'Successfully logged out.');
    }
  
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $success = $this->respondWithToken(auth()->refresh());
   
        return $this->sendResponse($success, 'Refresh token return successfully.');
    }
  
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }

    /**
 * Update user profile.
 *
 * @return \Illuminate\Http\JsonResponse
 */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validator = FacadesValidator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return $this->sendError('Nama/Email sudah digunakan.', $validator->errors());
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return $this->sendResponse($user, 'Profil berhasil diperbarui.');
    }

    /**
     * Change user password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $user = auth()->user();
    
        $validator = FacadesValidator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);
    
        if ($validator->fails()) {
            return $this->sendError('Password harus 6 karakter atau lebih.', $validator->errors());
        }
    
        // Menggunakan Hash::check() untuk memverifikasi password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return $this->sendError('Current password is incorrect.', [], 400);
        }
    
        // Mengupdate password baru
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);
    
        return $this->sendResponse([], 'Password berhasil diperbarui.');
    }
    
        /**
     * Get all users (Admin only).
     *
     * @return \Illuminate\Http\JsonResponse
     */
/**
 * Get all users.
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function getUsers()
{
    $users = User::all(['id', 'name', 'email', 'role', 'perumahan_id', 'created_at']);

    return $this->sendResponse($users, 'Daftar pengguna berhasil diambil.');
}

/**
 * Reset user password.
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function resetUserPassword(Request $request, $id)
{
    $validator = FacadesValidator::make($request->all(), [
        'new_password' => 'required|min:6',
        'confirm_password' => 'required|same:new_password',
    ]);

    if ($validator->fails()) {
        return $this->sendError('Validation Error.', $validator->errors());
    }

    $user = User::find($id);
    if (!$user) {
        return $this->sendError('User not found.', [], 404);
    }

    $user->password = bcrypt($request->new_password);
    $user->save();

    return $this->sendResponse([], 'Password pengguna berhasil direset.');
}

public function deleteUser($id)
{
    $authUser = auth()->user();

    // Cek apakah user yang login adalah direktur
    if ($authUser->role !== 'Direktur') {
        return $this->sendError('Hanya direktur yang dapat menghapus pengguna.', [], 403);
    }

    $user = User::find($id);

    if (!$user) {
        return $this->sendError('User tidak ditemukan.', [], 404);
    }

    if ($user->id === $authUser->id) {
        return $this->sendError('Tidak dapat menghapus akun sendiri.', [], 403);
    }

    $user->delete();

    return $this->sendResponse([], 'User berhasil dihapus.');
}
}
