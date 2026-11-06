<?php

namespace Tests\Feature;

use Tests\TestCase;

/** Guards the brand rules: own fonts and colours, no AI or tooling references shipped. */
class DesignSignatureTest extends TestCase
{
    public function test_brand_fonts_and_palette(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));
        $this->assertStringContainsString('Playfair Display', $css);
        $this->assertStringContainsString('Albert Sans', $css);
        $this->assertStringContainsString('#2b1a11', $css);
        $this->assertStringNotContainsString('fonts.googleapis.com', $css);
    }

    public function test_no_ai_tooling_references_in_shipped_code(): void
    {
        $dirs = [app_path(), resource_path(), base_path('routes'), base_path('config'), base_path('database'), base_path('deploy')];
        // Stored rot13 so this guard does not itself contain the words it looks for.
        $needle = '/\b('.str_rot13('pynhqr|naguebcvp|bcranv|pungtcg').')\b/i';
        foreach ($dirs as $dir) {
            if (! is_dir($dir)) {
                continue;
            }
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS));
            foreach ($it as $file) {
                if ($file->isFile() && preg_match('/\.(php|js|css|md|txt|json|sh)$/', $file->getFilename())) {
                    $this->assertDoesNotMatchRegularExpression($needle, file_get_contents($file->getPathname()), $file->getPathname());
                }
            }
        }
        foreach ([str_rot13('PYNHQR.zq'), str_rot13('NTRAGF.zq'), str_rot13('.pynhqr')] as $f) {
            $this->assertFileDoesNotExist(base_path($f));
        }
    }
}
