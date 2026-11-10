<?php

/**
 * sidebar.php, the news sidebar with the media contact widget, and content-post.php, the template
 * part with the mobile sidebar button.
 *
 * @see sidebar.php
 * @see content-post.php
 */

declare(strict_types=1);

use function Brain\Monkey\Functions\stubEscapeFunctions;
use function Brain\Monkey\Functions\when;

/**
 * Render sidebar.php for a request.
 *
 * @param  array<string, mixed>  $state  Overrides for the defaults below.
 */
function sidebarRender(array $state = []): string
{
    $s = $state + ['has_sidebar' => true, 'pdf' => '', 'single' => true, 'contact' => ''];

    stubEscapeFunctions();
    when('uams_has_sidebar')->justReturn($s['has_sidebar']);
    when('get_the_ID')->justReturn(7);
    when('is_single')->justReturn($s['single']);
    when('get_field')->alias(fn (string $selector, int $id): mixed => $selector === 'news_release_pdf_url' && $id === 7 ? $s['pdf'] : 'wrong call');
    when('get_post_meta')->alias(fn (int $id, string $key, bool $single): mixed => $id === 7 && $key === 'media_contact' && $single ? $s['contact'] : 'wrong call');
    when('uams_sidebar_menu')->alias(function (): void {
        echo '[sidebar menu]';
    });
    when('dynamic_sidebar')->alias(function (string $id): void {
        echo '[widgets '.$id.']';
    });

    return squish(renderTemplate('sidebar.php'));
}

/** The contact widget around the people a media contact names. */
function sidebarContact(string $slug, string $people): string
{
    return '<div role="navigation" aria-label="sidebar_navigation" id="uams-contact-'.$slug.'" class="widget contact-widget">'
        .' <div class="contact-widget-inner">'
        .' <span> <h2 class="widgettitle">Media Contacts</h2> </span> '
        .$people
        .' </div><!-- /.contact-widget-inner --></div>';
}

/** One person in a contact widget. The mailto link is the lowercase address unless the template spells it otherwise. */
function sidebarPerson(string $name, array $phones, string $email, ?string $mailto = null): string
{
    $html = '<h3 class="\'person-name\'">'.$name.'</h3>';
    foreach ($phones as $label => $number) {
        $html .= ' <p><a href="tel:'.$number.'" class="person-phone">'.$label.': '.$number.'</a></p>';
    }

    return $html.' <p><a href="mailto:'.($mailto ?? strtolower($email)).'" class="person-email">'.$email.'</a></p>';
}

it('prints nothing when the page has no sidebar', function (): void {
    expect(sidebarRender(['has_sidebar' => false]))->toBe('');
});

it('prints the menu, the widget area and no contact for a post without a media contact', function (): void {
    expect(sidebarRender(['contact' => '']))->toBe(
        '<div class="col-md-4 uams-sidebar"> [sidebar menu] [widgets sidebar] </div>'
    );
});

it('prints the news release button on a single post that has a PDF', function (): void {
    expect(sidebarRender(['pdf' => 'https://example.test/release.pdf']))->toContain(
        '<div class="align-center widget"><a class="uams-btn btn-red btn-lg btn-pdf" target="_self" title="View News Release" href="https://example.test/release.pdf">View News Release</a></div>'
    );
});

it('prints no news release button when the post has no PDF', function (): void {
    expect(sidebarRender())->not->toContain('btn-pdf');
});

it('prints no news release button outside a single post', function (): void {
    expect(sidebarRender(['pdf' => 'https://example.test/release.pdf', 'single' => false]))->not->toContain('btn-pdf');
});

it('prints the Taylor and Caldwell contacts', function (): void {
    $leslie = sidebarPerson('Leslie W. Taylor', ['Office' => '501-686-8998', 'Mobile' => '501-951-7260'], 'Leslie@uams.edu');
    $liz = sidebarPerson('Liz Caldwell', ['Office' => '501-686-8995', 'Mobile' => '501-350-4364'], 'Liz@uams.edu');

    expect(sidebarRender(['contact' => 'Media Taylor-Caldwell']))->toContain(sidebarContact('taylor-caldwell', $leslie.' '.$liz));
});

it('prints the Taylor and Dupins contacts', function (): void {
    $leslie = sidebarPerson('Leslie W. Taylor', ['Office' => '501-686-8998', 'Mobile' => '501-951-7260'], 'Leslie@uams.edu');
    $katrina = sidebarPerson('Katrina Dupins', ['Office' => '501-686-8149', 'Mobile' => '501-920-6977', 'Pager' => '501-395-5989'], 'Katrina@uams.edu');

    expect(sidebarRender(['contact' => 'Media Taylor-Dupins']))->toContain(sidebarContact('taylor-dupins', $leslie.' '.$katrina));
});

it('prints the Peel and Dupins contacts', function (): void {
    $andrea = sidebarPerson('Andrea Peel', ['Office' => '501-686-8996', 'Mobile' => '501-351-7903'], 'Andrea@uams.edu', 'Andrea@uams.edu');
    $katrina = sidebarPerson('Katrina Dupins', ['Office' => '501-686-8149', 'Mobile' => '501-920-6977', 'Pager' => '501-395-5989'], 'Katrina@uams.edu');

    expect(sidebarRender(['contact' => 'Media Peel-Dupins']))->toContain(sidebarContact('peel-dupins', $andrea.' '.$katrina));
});

it('prints the Taylor and Peel contacts', function (): void {
    $leslie = sidebarPerson('Leslie W. Taylor', ['Office' => '501-686-8998', 'Mobile' => '501-951-7260'], 'Leslie@uams.edu');
    $andrea = sidebarPerson('Andrea Peel', ['Office' => '501-686-8996', 'Mobile' => '501-351-7903'], 'Andrea@uams.edu');

    expect(sidebarRender(['contact' => 'Media Taylor-Peel']))->toContain(sidebarContact('taylor-peel', $leslie.' '.$andrea));
});

it('prints no contact for a value that names no group', function (): void {
    expect(sidebarRender(['contact' => 'Media Nobody']))->not->toContain('contact-widget');
});

it('prints no contact when the media contact is the string null', function (): void {
    expect(sidebarRender(['contact' => 'null']))->not->toContain('contact-widget');
});

/**
 * Render content-post.php for a title.
 */
function contentPostRender(string $title, bool $boilerplate): string
{
    when('get_the_title')->justReturn($title);
    when('the_title')->alias(function () use ($title): void {
        echo $title;
    });
    when('uams_sidebar_menu_mobile')->alias(function (): void {
        echo '[mobile menu]';
    });
    when('the_content')->alias(function (): void {
        echo '[content]';
    });
    when('get_the_ID')->justReturn(7);
    when('get_post_meta')->alias(fn (int $id, string $key, bool $single): mixed => $id === 7 && $key === 'include_boilerplate' && $single ? ($boilerplate ? '1' : '') : 'wrong call');

    return squish(renderTemplate('content-post.php'));
}

it('prints the title, the cut title in the button, the mobile menu and the content', function (): void {
    expect(contentPostRender('A considerably longer headline about a new clinic', false))->toBe(
        '<h1>A considerably longer headline about a new clinic</h1>'
        .' <div id="mobile-sidebar"> <button id="mobile-sidebar-menu" class="visible-xs" aria-hidden="true" tabindex="1">'
        .' <div aria-hidden="true" id="ham"> <span></span> <span></span> <span></span> <span></span> </div>'
        .' <div id="mobile-sidebar-title" class="page_item"> A considerably longer... </div> </button>'
        .' <div id="mobile-sidebar-links" aria-hidden="true"> [mobile menu]</div> </div> [content]'
    );
});

it('prints a short title whole in the button', function (): void {
    expect(contentPostRender('Short', false))->toContain('<div id="mobile-sidebar-title" class="page_item"> Short </div>');
});

it('prints the boilerplate when the post includes it', function (): void {
    $output = contentPostRender('Short', true);

    expect($output)->toContain('<!-- #Begin UAMS Boilerplate --> <hr size="1" width="85%"/><br/> <p>UAMS is the state’s only health sciences university')
        ->toEndWith('<p class="center-text">###</p> <!-- #End UAMS Boilerplate -->');
});

it('prints no boilerplate when the post does not include it', function (): void {
    expect(contentPostRender('Short', false))->not->toContain('Boilerplate');
});
