<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class CmsPagesSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => '<h2>Welcome to AutoLogos</h2><p>AutoLogos is India’s leading online specialist for premium automotive emblems, car logos, sports monograms, chrome lettering, and car styling accessories.</p><p>Founded by automotive design enthusiasts, we are committed to delivering OE-specification replacement emblems that restore your car’s pristine elegance or personalize it with bespoke performance monograms.</p><h3>Why Choose AutoLogos?</h3><ul><li><strong>100% Genuine Materials:</strong> Multi-layer electroplated ABS chrome and solid zinc metal alloys engineered to withstand Indian weather.</li><li><strong>OEM Accurate Dimensions:</strong> Factory grade curvature, millimeter-accurate alignment pins and authentic typography.</li><li><strong>Pre-Applied 3M Automotive Adhesive:</strong> Industry-best bonding tape for lifetime hold.</li><li><strong>Pan-India Express Delivery:</strong> Safe bubble-wrapped packaging dispatched within 24 hours.</li></ul>',
                'meta_title' => 'About AutoLogos | India’s No. 1 Car Emblems & Logos Specialist',
                'meta_description' => 'Learn about AutoLogos, India’s top supplier for genuine car logos, sports monograms, and chrome emblem badges.',
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<h2>Privacy Policy</h2><p>At AutoLogos, your privacy is paramount. This Privacy Policy details how we collect, protect, and handle your personal data when browsing or making a purchase on our website.</p><p>We collect essential order information including your name, shipping address, mobile number, and email address solely to process shipments, generate tax invoices, and communicate real-time order updates.</p><p>We do not store complete credit card or net banking details on our servers; all payment transactions are securely handled by RBI-regulated payment gateways via 256-bit SSL encryption.</p>',
            ],
            [
                'title' => 'Terms & Conditions',
                'slug' => 'terms-conditions',
                'content' => '<h2>Terms and Conditions of Sale</h2><p>By placing an order with AutoLogos, you agree to these Terms and Conditions. All prices listed on our website are in Indian Rupees (INR) and are inclusive of applicable Goods and Services Tax (GST).</p><p>Orders are confirmed upon successful online payment authorization or selection of Cash on Delivery (where eligible). Dispatch timelines range from 24 to 48 hours on business days.</p>',
            ],
            [
                'title' => 'Shipping Policy',
                'slug' => 'shipping-policy',
                'content' => '<h2>Shipping & Delivery Policy</h2><p>We provide fast and secure delivery across all pincodes in India via trusted courier partners including BlueDart, Delhivery, DTDC, and India Post Speed Post.</p><ul><li><strong>Free Shipping:</strong> On all orders totaling &#x20B9;999 and above.</li><li><strong>Standard Shipping:</strong> A flat &#x20B9;99 fee applies on orders below &#x20B9;999.</li><li><strong>Delivery Timelines:</strong> Metro cities: 2-3 business days. Non-metro and regional locations: 3-5 business days.</li><li><strong>Tracking:</strong> Real-time tracking IDs are provided via WhatsApp and SMS immediately upon dispatch.</li></ul>',
            ],
            [
                'title' => 'Return & Refund Policy',
                'slug' => 'return-refund-policy',
                'content' => '<h2>7-Day Easy Returns & Replacement Policy</h2><p>We stand behind the quality of every emblem we craft. If you receive a damaged, mismatched, or defective emblem, you can request a return or replacement within <strong>7 days</strong> of delivery.</p><h3>Return Conditions:</h3><ul><li>The item must be unused, with the red 3M peel-off adhesive film intact.</li><li>Include the original product packaging and invoice slip.</li><li>Approved refunds are processed to your original payment mode within 3-5 business days.</li></ul>',
            ],
            [
                'title' => 'FAQ',
                'slug' => 'faq',
                'content' => '<h2>Frequently Asked Questions</h2><h4>1. Will the emblem fit my specific car model?</h4><p>Yes, all our emblems are crafted to OEM dimensions. Please review the compatible models listed in the product description before ordering.</p><h4>2. Is the chrome plating weather and car-wash resistant?</h4><p>Yes, our badges feature automotive-grade electroplated chrome designed to withstand extreme sunlight, rain, dust, and pressure washing without peeling or fading.</p><h4>3. How do I apply the emblem?</h4><p>Ensure the surface is thoroughly cleaned with isopropyl alcohol and completely dry. Peel off the red protective film from the pre-applied 3M adhesive backing and firmly press the emblem into place for 30 seconds.</p><h4>4. Is Cash on Delivery (COD) available?</h4><p>Yes, COD is available across most serviceable Indian pincodes.</p>',
            ],
        ];

        foreach ($pages as $p) {
            CmsPage::updateOrCreate(['slug' => $p['slug']], $p);
        }
    }
}
