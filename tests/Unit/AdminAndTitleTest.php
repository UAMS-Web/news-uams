<?php

/**
 * The admin columns, the ACF editor script and text_cut(), the title shortener behind the mobile
 * sidebar button.
 *
 * @see functions.php
 */

declare(strict_types=1);

use Brain\Monkey\Functions;

it('adds the Featured Image column after the first column', function (): void {
    expect(add_img_column(['cb' => '<input>', 'title' => 'Title', 'date' => 'Date']))->toBe([
        'cb' => '<input>',
        'img' => 'Featured Image',
        'title' => 'Title',
        'date' => 'Date',
    ]);
});

it('prints the thimble thumbnail in the Featured Image column and returns the column name', function (): void {
    Functions\expect('get_the_post_thumbnail')->once()->with(42, 'thimble')->andReturn('<img src="t.jpg">');

    ob_start();
    $returned = manage_img_column('img', 42);
    $printed = ob_get_clean();

    expect($printed)->toBe('<img src="t.jpg">')
        ->and($returned)->toBe('img');
});

it('prints nothing in any other column and returns the column name', function (): void {
    Functions\expect('get_the_post_thumbnail')->never();

    ob_start();
    $returned = manage_img_column('title', 42);
    $printed = ob_get_clean();

    expect($printed)->toBe('')
        ->and($returned)->toBe('title');
});

it('moves the editor into the ACF field and clears its toolbar background', function (): void {
    ob_start();
    my_acf_admin_head();
    $printed = (string) ob_get_clean();

    expect($printed)->toContain("\$('.acf-field-573f70ac9357c .acf-input').append( \$('#postdivrich') );")
        ->toContain('.acf-field #wp-content-editor-tools {')
        ->toContain('background: transparent;')
        ->toContain('padding-top: 0;');
});

it('leaves a title shorter than the limit whole', function (): void {
    expect(text_cut('Short title', 27, true))->toBe('Short title');
});

it('cuts a long title at a space within the limit and adds an ellipsis', function (): void {
    expect(text_cut('A considerably longer headline about a new clinic', 27, true))->toBe('A considerably longer...');
});

it('cuts without an ellipsis when dots are off', function (): void {
    expect(text_cut('A considerably longer headline about a new clinic', 27, false))->toBe('A considerably longer');
});

it('collapses runs of two or more whitespace characters before cutting', function (): void {
    expect(text_cut("Two  spaces\tand\n\na break here and more words to cut", 20, false))->toBe("Two spaces\tand a");
});

it('keeps a single newline, which is not collapsed, so no space is found at that position', function (): void {
    expect(text_cut("One\nTwo Three Four", 8, false))->toBe("One\nTwo");
});

it('cuts a title with no space in it at the limit', function (): void {
    expect(text_cut('Superlongwordwithoutspaces', 10, true))->toBe('Superlongw...')
        ->and(text_cut('Superlongwordwithoutspaces', 10, false))->toBe('Superlongw');
});

it('cuts at the limit when the only space lies beyond it', function (): void {
    expect(text_cut('Hello world', 3, true))->toBe('Hel...');
});

it('cuts at a space in the second position', function (): void {
    expect(text_cut('a bcdef', 4, true))->toBe('a...');
});

it('returns an empty string for a limit of zero or less', function (int $limit): void {
    expect(text_cut('Superlongwordwithoutspaces', $limit, true))->toBe('');
})->with([0, -1, -40]);

it('returns an empty string for an empty title', function (): void {
    expect(text_cut('', 27, true))->toBe('');
});

it('trims the title before cutting', function (): void {
    expect(text_cut("  Padded title  \n", 27, true))->toBe('Padded title');
});

it('counts the ellipsis only when the cut removed something', function (): void {
    expect(text_cut('Exactly twenty-seven char', 27, true))->toBe('Exactly twenty-seven char');
});
