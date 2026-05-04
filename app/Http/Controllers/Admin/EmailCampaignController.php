<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CampaignMail;
use App\Models\EmailCampaign;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmailCampaignController extends Controller
{
    public function index()
    {
        $campaigns = EmailCampaign::latest()->paginate(15);

        return Inertia::render('Admin/Emails/Index', [
            'campaigns' => $campaigns,
        ]);
    }

    public function create()
    {
        $counts = [
            'all'     => User::where('is_active', true)->count(),
            'free'    => User::where('is_active', true)->where(fn($q) => $q->where('subscription_type', 'free')->orWhereNull('subscription_type'))->count(),
            'pro'     => User::where('is_active', true)->where('subscription_type', 'pro')->count(),
            'expired' => User::where('is_active', true)->where('subscription_type', 'expired')->count(),
        ];

        return Inertia::render('Admin/Emails/Create', [
            'counts' => $counts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'subject'         => 'required|string|max:255',
            'preheader'       => 'nullable|string|max:255',
            'body_heading'    => 'nullable|string|max:255',
            'body_content'    => 'required|string',
            'cta_text'        => 'nullable|string|max:100',
            'cta_url'         => 'nullable|url|max:500',
            'target_audience' => 'required|in:all,free,pro,expired',
            'scheduled_at'    => 'nullable|date|after:now',
            'send_now'        => 'boolean',
        ]);

        $status = ($request->boolean('send_now')) ? 'sending' : ($validated['scheduled_at'] ? 'scheduled' : 'draft');

        $campaign = EmailCampaign::create([
            ...$validated,
            'status' => $status,
        ]);

        if ($request->boolean('send_now')) {
            $this->dispatchCampaign($campaign);
            return redirect()->route('admin.emails.index')->with('success', 'Campaign sent successfully!');
        }

        return redirect()->route('admin.emails.index')->with('success', 'Campaign saved as ' . $status);
    }

    public function preview(EmailCampaign $campaign)
    {
        $fakeUser = new User(['name' => 'Preview User', 'email' => 'preview@example.com']);
        $mail = new CampaignMail($fakeUser, $campaign);

        return $mail->render();
    }

    public function send(EmailCampaign $campaign, MailService $mailService)
    {
        if ($campaign->status === 'sent') {
            return back()->with('error', 'Campaign already sent.');
        }

        $this->dispatchCampaign($campaign);

        return back()->with('success', 'Campaign is being sent!');
    }

    public function destroy(EmailCampaign $campaign)
    {
        if ($campaign->status === 'sent') {
            return back()->with('error', 'Cannot delete sent campaigns.');
        }

        $campaign->delete();

        return back()->with('success', 'Campaign deleted.');
    }

    public function duplicate(EmailCampaign $campaign)
    {
        $new = $campaign->replicate();
        $new->name = $campaign->name . ' (Copy)';
        $new->status = 'draft';
        $new->sent_at = null;
        $new->total_recipients = 0;
        $new->save();

        return redirect()->route('admin.emails.index')->with('success', 'Campaign duplicated.');
    }

    private function dispatchCampaign(EmailCampaign $campaign): void
    {
        $users = $this->getTargetUsers($campaign->target_audience);

        app(MailService::class)->sendCampaign($campaign, $users);
    }

    private function getTargetUsers(string $audience)
    {
        $query = User::where('is_active', true);

        return match ($audience) {
            'free'    => $query->where(fn($q) => $q->where('subscription_type', 'free')->orWhereNull('subscription_type'))->get(),
            'pro'     => $query->where('subscription_type', 'pro')->get(),
            'expired' => $query->where('subscription_type', 'expired')->get(),
            default   => $query->get(),
        };
    }
}
