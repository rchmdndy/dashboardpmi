<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function postReview(Request $request){

        $validator = Validator::make($request->all(),[
            'user_email' => 'required|email|exists:users,email',
            'user_transaction_id' => 'required|exists:user_transactions,id',
            'review' => 'required|string',
            'score' => 'required|integer|min:1|max:5'
        ]);

        if($validator->fails()){
            $error = $validator->errors();
            return match ($error) {
                $error->has("user_email") => response()->json(["error" => "Email tidak valid"], 400),
                $error->has("user_transaction_id") => response()->json(["error" => "Transaksi tidak valid"], 400),
                $error->has("review") => response()->json(["error" => "Review tidak valid"], 400),
                $error->has("score") => response()->json(["error" => "Score tidak valid"], 400),
                default => response()->json(["error" => $error->getMessages()], 400),
            };
        }
        $review = Review::where("user_transaction_id", $request->user_transaction_id)->first();
        if (!$review) {
            Review::create([
                "user_email" => $request->user_email,
                "user_transaction_id" => $request->user_transaction_id,
                "review" => $request->review,
                "score" => $request->score
            ]);
            return response()->json(["Review berhasil!"]);
        }else{
            Review::where("user_transaction_id", $request->user_transaction_id)->update([
                "review" => $request->review,
                "score" => $request->score
            ]);
            return response()->json(["Update Review berhasil!"]);
        }
    }

    public function getReviewForCurrentTransaction(Request $request){
        $validator = Validator::make($request->all(), [
            "user_transaction_id" => "required|exists:user_transactions,id"
        ]);

        if ($validator->fails()) return response()->json(["error" => "Data transaksi tidak ditemukan"], 400);
        $review = Review::where("user_transaction_id", $request->user_transaction_id)->first();
        if (!$review) {
            return response()->json(["status" => false]);
        }else{
            return response()->json([
               "status" => true,
                "review" => $review->review,
                "score" => $review->score
            ]);
        }
    }

    public function getTopReview(){
        $reviewdata = [];
        $reviewCount = Review::count();
        if($reviewCount < 1) {
            return response(null, 200);
        } else {
            $reviews = Review::where("score", "=", 5)->get();
            if ($reviews->count() < 1) return response()->json(['count' => 0]);
            foreach ($reviews as $review) {
                $data = [
                    "name" => $review->user->name,
                    "score" => $review->score,
                    "review" => $review->review
                ];
                $reviewdata[] = $data;
            }
            return response()->json($reviewdata);
        }
    }
}
