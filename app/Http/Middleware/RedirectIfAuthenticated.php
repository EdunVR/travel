<?php
// Add to handle() method
if (Auth::guard('investor')->check()) {
    return redirect()->route('investor.dashboard');
}