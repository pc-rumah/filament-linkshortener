<p>Halo {{ $transaction->user->name }},</p>

<p>
    Terima kasih, pembayaran kamu untuk <strong>Plan Pro</strong> telah berhasil.
</p>

<p>
    Invoice terlampir pada email ini.
</p>

<p>
    Plan aktif sampai:
    <strong>{{ now()->addMonth()->format('d M Y') }}</strong>
</p>

<p>Terima kasih 🙏</p>
