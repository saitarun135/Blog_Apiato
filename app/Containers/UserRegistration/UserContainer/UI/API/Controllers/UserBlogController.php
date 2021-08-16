<?php

namespace App\Containers\UserRegistration\UserContainer\UI\API\Controllers;
use App\Ship\Parents\Controllers\ApiController;
use App\Containers\UserRegistration\UserContainer\Models\UserContainer;
use Illuminate\Http\Request;
use Validator;
use JWTAuth;
use DB;
use Hash;
use App\Containers\UserRegistration\UserContainer\Models\BlogModel;
use App\Containers\UserRegistration\UserContainer\Models\AdminModel;
use Illuminate\Support\Facades\Cache;
class UserBlogController extends ApiController
{
    public function register(Request $request)
    {
        $user = new UserContainer([
            'fullName' => $request->input('fullName'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'mobile' => $request->input('mobile'),
        ]);
        $userMail=UserContainer::where('email',$user->email)->first();
        if($userMail){
            return response()->json(['Alert'=>"This email is already Taken"]);
        }
        
        $user->save();
        return response()->json(['message' => 'user registered successfully']);
    }

    public function login(Request $request)
    {
    
        $req = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $email = $request->get('email');
        $user = UserContainer::where('email', $email)->first();
        $password = $request->get('password');
        $userPassword = UserContainer::where('email', $email)->value('password');
        if (!Hash::check($password, $userPassword)) {
            return response()->json(['message' => "please check your  password"]);
        }
        if (!$user) {
            return response()->json(['status' => 400, 'message' => "Invalid credentials! email doesn't exists"]);
        }
        if ($req->fails()) {
            return response()->json(['status' => 403, 'message' => "please enter the valid details"]);
        }
        $token = JWTAuth::fromUser($user);
        if (!$token) {
            return response()->json(['status' => 401, 'message' => 'Unauthenticated']);
        }
        return $this->generateToken($token);
    }

    public function adminRegistration(Request $request){
        $this->validate($request, [
            'firstName'=>'required|string|between:3,15',
            'lastName'=>'required|string|between:3,15',
            'email'=>'required|email',
            'password'=>'required|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'mobile'=>'required|digits:10',
            ]);   
            
        $admin = new AdminModel([
            'firstName' => $request->input('firstName'),
            'lastName'=>$request->input('lastName'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'mobile' => $request->input('mobile'),
        ]);
        $adminMail = AdminModel::where('email', $admin->email)->first();
        if($adminMail){
            return response()->json(['Alert'=>'This email is already Taken']);
        }
        $admin->save();
        return response()->json(['message' => 'Admin registered successfully']);
    }
    public function adminLogin(Request $request){
        $req = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $email = $request->get('email');
        $user = AdminModel::where('email', $email)->first();
        $password = $request->get('password');
        $userPassword = AdminModel::where('email', $email)->value('password');
        if (!Hash::check($password, $userPassword)) {
            return response()->json(['message' => "please check your  password"]);
        }
        if (!$user) {
            return response()->json(['status' => 400, 'message' => "Invalid credentials! email doesn't exists"]);
        }
        if ($req->fails()) {
            return response()->json(['status' => 403, 'message' => "please enter the valid details"]);
        }
        $token = JWTAuth::fromUser($user);
        if (!$token) {
            return response()->json(['status' => 401, 'message' => 'Unauthenticated']);
        }
        return $this->generateAdminToken($token);
    }

    public function generateAdminToken($token){
        return response()->json([
            'message'=>'welcome to Admin Dashboard',
            'token'=>$token
        ]);
    }
    public function generateToken($token)
    {
        return response()->json([
            'message' => 'succesfully logged in...',
            'token' => $token
        ]);
    }
    public function upload(Request $request)
    {
        $blog = new BlogModel();
        $blog->name = $request->input('name');
        $blog->price = $request->input('price');
        $blog->image = $request->input('image');
        $blog->rating = $request->input('rating');
        $blog->country = $request->input('country');
        $blog->description = $request->input('description');
        $token=$request->bearerToken();
        $tokenParts = explode(".", $token); 
        //$tokenHeader = base64_decode($tokenParts[0]);
        $tokenPayload = base64_decode($tokenParts[1]);
        //$jwtHeader = json_decode($tokenHeader);
        $jwtPayload = json_decode($tokenPayload);
        $blog->admin_id=$jwtPayload->sub;
        $blog->save();
        return response()->json(['success' => 'Blog Added successfully']);
    }
  
     public function updateBlog(Request $request,$id){
         $blog=BlogModel::findOrFail($id);
         $token=$request->bearerToken();
         $tokenParts = explode(".", $token); 
         $tokenPayload = base64_decode($tokenParts[1]);
         $jwtPayload = json_decode($tokenPayload);
         if($blog->admin_id==$jwtPayload->sub){
             $blog=BlogModel::where('id',$id)->update(
                 array('name'=>$request->input('name'),
                       'price'=>$request->input('price'),
                       'rating'=>$request->input('rating'),
                       'image'=>$request->input('image'),
                       'description'=>$request->input('description')
                 )
             );
             return['Blog updated successfully'];
         }
     }
    public function deleteBlog(Request $request,$id){
        $book=BlogModel::findOrFail($id);
        $token=$request->bearerToken();
        $tokenParts = explode(".", $token); 
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtPayload = json_decode($tokenPayload);
        if($book->admin_id==$jwtPayload->sub){
            if($book->delete()){
                return response()->json(['message'=>'Blog Deleted successfully'],201);
            }
        }
        else{
            return response()->json([
                'error' => ' Method Not Allowed/invalid Blog id'], 405);
        }
    }
    public function displayAdminBlogs(){
        $admin = new BlogModel();
        $token = JWTAuth::getToken();
        if(!$token){
            return response()->json(['message'=>"Please provide token/Invalid token"]);
        }
        $id = JWTAuth::getPayload($token)->toArray();
        $admin->admin_id = $id["sub"];
        
        return DB::table('blogs_table')->where('admin_id',$admin->admin_id)->get();
    }

    public function displayUserBlogs(){ 
        //cache::set('data',DB::table('blogs_table')->paginate(6));
        //return  Cache::get('data');
        return DB::table('blogs_table')->get();
    }

    public function displayBlogByID(Request $request,$id){
        $blogs=BlogModel::findOrFail($id);
        return DB::table('blogs_table')->where('id',$blogs->id)->get();
    }
  
}
