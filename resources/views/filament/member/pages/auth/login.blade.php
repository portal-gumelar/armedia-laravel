<div class="min-h-screen w-full flex items-center justify-center p-4 sm:p-8" style="background-color: #f5f5f0;">
    <div class="w-full max-w-[420px] rounded-xl overflow-hidden flex flex-col" style="box-shadow: 0 20px 40px -10px rgba(0,0,0,0.15);">
        
        <!-- Header Section (Navy Blue) -->
        <div class="p-10 flex flex-col items-center justify-center text-center relative" style="background-color: #1c2b4a;">
            <!-- Golden Circle Logo -->
            <div class="w-16 h-16 rounded-full flex items-center justify-center mb-6 relative z-10" style="background-color: #ffffff; border: 2px solid #d4af37; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                <!-- Assuming logo-armedia.png might be dark, we can use a stylized 'A' or the logo directly -->
                <span class="text-3xl font-serif italic" style="color: #d4af37;">A</span>
            </div>
            
            <h1 class="text-[1.1rem] font-bold tracking-widest uppercase relative z-10" style="color: #d4af37; letter-spacing: 0.15em;">
                ARMEDIA PORTAL
            </h1>
            <p class="text-[0.65rem] font-bold mt-2 tracking-widest uppercase relative z-10" style="color: #7b8eaf; letter-spacing: 0.15em;">
                Internal Management Portal
            </p>
        </div>

        <!-- Form Section (White) -->
        <div class="p-8 sm:px-10 sm:py-8 bg-white relative">
            
            <!-- Separator with Text -->
            <div class="flex items-center mb-8 mt-2">
                <div class="flex-grow border-t border-gray-200/80"></div>
                <span class="flex-shrink-0 px-3 text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">
                    Authorized Access Only
                </span>
                <div class="flex-grow border-t border-gray-200/80"></div>
            </div>

            <style>
                /* Overriding Filament default styles to match the mockup */
                .filament-login-form .fi-fo-field-wrp-label label {
                    font-size: 0.7rem !important; 
                    font-weight: 700 !important; 
                    text-transform: uppercase !important;
                    color: #1c2b4a !important; /* navy blue */
                    letter-spacing: 0.1em !important;
                    margin-bottom: 0.5rem !important;
                }
                .filament-login-form .fi-input-wrp {
                    border-radius: 0.4rem !important; 
                    border: none !important;
                    background-color: #eef2f6 !important; /* light blue/gray background */
                    box-shadow: none !important;
                    overflow: hidden !important;
                    transition: all 0.2s ease;
                }
                .filament-login-form .fi-input-wrp:focus-within {
                    background-color: #e6ebf1 !important;
                    box-shadow: 0 0 0 2px rgba(28, 43, 74, 0.1) !important;
                }
                .filament-login-form .fi-input-wrp input {
                    background-color: transparent !important;
                    padding: 0.65rem 1rem !important;
                    font-size: 0.875rem !important; 
                    color: #1e293b !important;
                    font-weight: 500 !important;
                }
                .filament-login-form .fi-input-wrp input:focus {
                    box-shadow: none !important;
                    outline: none !important;
                }
                .filament-login-form .fi-btn {
                    background-color: #1c2b4a !important; /* navy blue */
                    color: #d4af37 !important; /* gold text */
                    border-radius: 0.35rem !important;
                    font-weight: 700 !important;
                    text-transform: uppercase !important;
                    letter-spacing: 0.1em !important;
                    padding: 0.75rem !important;
                    box-shadow: 0 4px 12px rgba(28, 43, 74, 0.2) !important;
                    transition: all 0.2s ease-in-out !important;
                    margin-top: 1rem !important;
                }
                .filament-login-form .fi-btn:hover {
                    background-color: #131e36 !important;
                    box-shadow: 0 6px 16px rgba(28, 43, 74, 0.3) !important;
                }
                /* Hide remember me / forgot password if not strictly needed or restyle them */
                .filament-login-form .fi-fo-field-wrp:has(input[type="checkbox"]) {
                    display: none !important;
                }
            </style>

            <div class="filament-login-form">
                <x-filament-panels::form id="form" wire:submit="authenticate">
                    {{ $this->form }}

                    <div class="mt-8">
                        <x-filament-panels::form.actions
                            :actions="$this->getCachedFormActions()"
                            :full-width="$this->hasFullWidthFormActions()"
                        />
                    </div>
                </x-filament-panels::form>
            </div>
            
        </div>
    </div>
</div>
