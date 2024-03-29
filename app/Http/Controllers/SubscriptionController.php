<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Event;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Configuration;
use App\Utils\Pix\Payload;

class SubscriptionController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Event $event
     * @return \Illuminate\Http\Response
     */
    public function index(Event $event)
    {
        $subscriptions = $event->subscriptions()->paginate(6);
        return view('admin.subscription.index', [
            'event' => $event,
            'subscriptions' => $subscriptions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Event $event)
    {
        return view('public.event.subscribe', [
            'event' => $event,
            'user' => User::find(auth()->user()->id)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Event $event)
    {        
        if ($event->registration_fee > 0) {
            $paymentType = $request->paymentType;
            $status = Subscription::STATUS_AGUARDANDO;            
        } else {
            $paymentType = Subscription::PAYMENT_NENHUM;
            $status = Subscription::STATUS_PAGO;
        }         
        $subscription = $event->subscriptions()->create([
            'status' => $status,
            'payment_type' => $paymentType,
            'user_id' => auth()->user()->id
        ]);
        return redirect()->route('subscriptions.show', $subscription);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Subscription $subscription
     * @return \Illuminate\Http\Response
     */
    public function show(Subscription $subscription)
    {
        $pixKey = Configuration::firstWhere('name', 'pixKey')->value;
        $merchantName = Configuration::firstWhere('name', 'pixMerchantName')->value;
        $merchantCity = Configuration::firstWhere('name', 'pixMerchantCity')->value;
        $payload = ($pixKey == '' || $merchantName == '' || $merchantCity == '') ? '' : (new Payload())
            ->setPixKey($pixKey)
            ->setDescription('Inscrição ' . $subscription->id . ' - ' . $subscription->event->name)
            ->setMerchantName($merchantName)
            ->setMerchantCity($merchantCity)
            ->setAmount($subscription->event->registration_fee)
            ->setTxid('E' . $subscription->event->id . 'I' . $subscription->id)->getPayload();
        return view('admin.subscription.show', [
            'subscription' => $subscription,
            'payload' => $payload
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Subscription $subscription
     * @return \Illuminate\Http\Response
     */
    public function edit(Subscription $subscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subscription $subscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subscription $subscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Subscription $subscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return redirect()->route('admin.home');
    }


    /** 
     * 
     * @param Subscription $subscription
     * @return \Illuminate\Http\Response
     */ 
    public function paymentForm(Subscription $subscription)
    {
        return view('public.subscription.payment', [
            'subscription' => $subscription
        ]);
    }

    /**
     * 
     * @param Request $request
     * @param Subscription $subscription
     * @return \Illuminate\Http\Response
     */
    public function payment(Request $request, Subscription $subscription)
    {
        $request->validate([
            'payment' => 'required|image|max:2000'
        ]);
        if ($request->file('payment')) {
            $image = $request->file('payment');
            $imageName = time() . '-pay.' . $image->getClientOriginalExtension();            
            if ($subscription->image_path != '') {
                Storage::delete($subscription->image_path);
            }
            $imagePath = Storage::putFileAs('payments', $image, $imageName, 'public');
            $subscription->update([
                'image_name' => $imageName,
                'image_path' => $imagePath
            ]);
        }
        return redirect()->route('subscriptions.show', $subscription);
    }

    public function confirmationForm(Subscription $subscription)
    {
        return view('admin.subscription.confirmation', [
            'subscription' => $subscription
        ]);
    }

    public function confirmation(Request $request, Subscription $subscription)
    {        
        $subscription->update([
            'status' => 'Pago'
        ]);
        return redirect()->route('events.subscriptions.index', $subscription->event);
    }

}
