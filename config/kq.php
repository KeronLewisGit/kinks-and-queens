<?php

/*
| Site copy that is not edited in the admin. Facts come from the brand's own
| Instagram posts and flyers (June to November 2026); nothing here is invented
| pricing. Prices live in the database (Admin → Price lists).
*/

return [

    'name' => 'Kinks & Queens',
    'tagline' => 'Where Texture Reigns',
    'country' => 'Trinidad & Tobago',

    'promise' => 'We believe in enhancing your natural beauty, not changing it. We specialise in premium textured hair extensions that blend seamlessly, feel luxurious and celebrate every beautiful curl.',

    'diary' => [
        ['when' => 'Day 1 to 7', 'texture' => 'brazilian-kinky-curly', 'image' => 'assets/img/kinky-curly-flip.webp',
            'title' => 'A week of curls, almost no upkeep',
            'body' => 'Bonnet off, a spritz of sheen spray, edges laid, then shake, fluff and go. No curl creams, gels, mousses or daily restyling for seven days.'],
        ['when' => 'Day 10', 'texture' => 'brazilian-kinky-curly', 'image' => 'assets/img/kinky-curly-red.webp',
            'title' => 'Still going strong',
            'body' => 'Ten days in with no curl redefining. The volume and texture held, because you deserve to know exactly what you are investing in.'],
        ['when' => 'Wash day', 'texture' => 'brazilian-kinky-curly', 'image' => 'assets/img/kinky-curly-closeup.webp',
            'title' => 'Washed and air-dried',
            'body' => 'No heavy styling, no curl wand, no manipulation. Just the texture doing what it does in its natural state.'],
        ['when' => 'Grown out', 'texture' => 'brazilian-textured-straight', 'image' => 'assets/img/straight-blend.webp',
            'title' => '“What tracks?”',
            'body' => 'Three tracks of Textured Straight for length and body. Even when the install was ready to come down, the blend was still seamless.'],
        ['when' => 'Two years', 'texture' => 'mongolian-afro-coily', 'image' => 'assets/img/afro-coily-updo.webp',
            'title' => 'Still in rotation',
            'body' => 'A client has worn her Afro Coily bundles for about two years and still reaches for them, most recently for this polished updo.'],
    ],

    'care' => [
        ['title' => 'Sleep in a bonnet', 'body' => 'Satin keeps curls from frizzing and tangling overnight, so day two looks like day one.'],
        ['title' => 'A light spritz of sheen', 'body' => 'A little sheen spray in the morning brings back shine without weighing the curls down.'],
        ['title' => 'Lay your edges', 'body' => 'Blend your own hairline into the install for that grew-from-my-scalp finish.'],
        ['title' => 'Shake, fluff and go', 'body' => 'Fluff from the root with your fingers. If you love a fuller, lived-in look, you rarely need to redefine.'],
    ],

    'wash' => [
        'Detangle gently with your fingers or a wide-tooth comb, starting from the ends and working up.',
        'Wash with a moisturising, sulphate-free shampoo and follow with conditioner. Squeeze, do not rub.',
        'Let curly and coily textures air-dry in their natural state to keep the pattern defined.',
        'Use heat protectant before curling or pressing Textured Straight, and keep the heat moderate.',
        'Planning to colour or lighten? Ask a professional colourist. Our Kinky Curly and Textured Straight have both taken colour well for clients.',
        'Store clean, dry hair loosely in a satin bag so it is ready for the next install.',
    ],

    'faqs' => [
        ['q' => 'Do you have hair in stock?', 'a' => 'No. Every order is made by pre-order and we do not carry ready stock. That keeps prices fair and means your hair is ordered for you.'],
        ['q' => 'How long does a pre-order take?', 'a' => 'Orders are typically received within 14 business days or less once your order is confirmed and shipped. You can follow every step on the tracking page.'],
        ['q' => 'How do I place an order?', 'a' => 'Send a pre-order request on this site (or DM us on Instagram). We message you to confirm texture, lengths and price before anything is ordered.'],
        ['q' => 'How much hair is in one bundle?', 'a' => 'Brazilian Kinky Curly and Brazilian Textured Straight bundles are 98–100 g each. Mongolian Afro Coily bundles are 95–100 g.'],
        ['q' => 'How many bundles do I need?', 'a' => 'Our price lists are built around 3-bundle sets, with 4-bundle sets of Kinky Curly for longer, fuller looks and 2-bundle sets of Textured Straight for a lighter top-up. Tell us the look you want and we will advise.'],
        ['q' => 'Is it human hair?', 'a' => 'Yes. Every texture is 100% human hair.'],
        ['q' => 'Can I colour or lighten it?', 'a' => 'Yes, with care. Clients have lightly bleached Brazilian Kinky Curly and lifted the colour of Brazilian Textured Straight. We recommend a professional colourist.'],
        ['q' => 'Which texture is right for me?', 'a' => 'Kinky Curly is made for 4A–4B hair, Afro Coily for 4C, and Textured Straight for relaxed, silk-pressed or heat-trained hair. Take the Texture Match quiz if you are between two.'],
        ['q' => 'What do ponytails and clip-ins cost?', 'a' => 'They are made to order, so we quote them when we confirm your request. Send a request or DM us for pricing.'],
        ['q' => 'Can you turn my old bundles into clip-ins?', 'a' => 'Yes. We machine-sew your bundles into a custom clip-in set of about 10–13 pieces, double or triple tracked for the fullness you want.'],
        ['q' => 'How do payment and delivery work?', 'a' => 'We agree payment and collection or delivery with you directly when we confirm your order, before anything is ordered.'],
    ],

    'quiz' => [
        [
            'key' => 'hair',
            'q' => 'How do you wear your own hair most days?',
            'options' => [
                ['value' => 'kc', 'label' => 'Natural, defined curls and coils', 'hint' => 'Roughly 4A–4B'],
                ['value' => 'ac', 'label' => 'Natural, tight coils with lots of shrinkage', 'hint' => 'Roughly 4C'],
                ['value' => 'ts', 'label' => 'Relaxed, silk-pressed or heat-trained', 'hint' => 'Smooth with natural movement'],
            ],
        ],
        [
            'key' => 'look',
            'q' => 'Which look are you going for?',
            'options' => [
                ['value' => 'kc', 'label' => 'Big, bouncy, voluminous curls', 'hint' => 'The wash-and-go look'],
                ['value' => 'ac', 'label' => 'A full coily blend, puff or updo', 'hint' => 'Density and definition'],
                ['value' => 'ts', 'label' => 'Sleek, soft curls or colour', 'hint' => 'Polished, ready in minutes'],
            ],
        ],
        [
            'key' => 'wear',
            'q' => 'How do you want to wear it?',
            'options' => [
                ['value' => 'bundles', 'label' => 'A sew-in or install', 'hint' => 'Bundles'],
                ['value' => 'ponytail', 'label' => 'A quick change I can put on in minutes', 'hint' => 'Drawstring ponytail'],
                ['value' => 'clip-ins', 'label' => 'Something I can take out at night', 'hint' => 'Clip-ins'],
            ],
        ],
    ],

    'stats' => [
        ['value' => '100%', 'label' => 'human hair'],
        ['value' => '3', 'label' => 'signature textures'],
        ['value' => '≤14', 'label' => 'business days, pre-order to you'],
    ],
];
