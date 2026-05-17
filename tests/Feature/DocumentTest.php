<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    // ─── Store ───────────────────────────────────────────────────────────────

    public function test_user_can_upload_a_cv(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('resume.pdf', 512, 'application/pdf');

        $response = $this->actingAs($user)->post(route('documents.store'), [
            'document_name' => 'My CV',
            'type'          => 'cv',
            'file'          => $file,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('documents', [
            'user_id'       => $user->id,
            'document_name' => 'My CV',
            'type'          => 'cv',
            'is_primary'    => true,
        ]);
    }

    public function test_uploading_new_cv_clears_old_primary_flag(): void
    {
        Storage::fake('local');

        $user    = User::factory()->create();
        $oldFile = UploadedFile::fake()->create('old.pdf', 256, 'application/pdf');
        $newFile = UploadedFile::fake()->create('new.pdf', 256, 'application/pdf');

        // Upload first CV
        $this->actingAs($user)->post(route('documents.store'), [
            'document_name' => 'Old CV',
            'type'          => 'cv',
            'file'          => $oldFile,
        ]);

        $oldDoc = $user->documents()->where('type', 'cv')->first();
        $this->assertTrue((bool) $oldDoc->is_primary);

        // Upload second CV
        $this->actingAs($user)->post(route('documents.store'), [
            'document_name' => 'New CV',
            'type'          => 'cv',
            'file'          => $newFile,
        ]);

        $this->assertDatabaseHas('documents', ['id' => $oldDoc->id, 'is_primary' => false]);
        $this->assertDatabaseHas('documents', ['document_name' => 'New CV', 'is_primary' => true]);
    }

    public function test_certificate_type_requires_certificate_id(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('cert.pdf', 512, 'application/pdf');

        $response = $this->actingAs($user)->post(route('documents.store'), [
            'document_name' => 'NC II Certificate',
            'type'          => 'certificate',
            // certificate_id intentionally omitted
            'file'          => $file,
        ]);

        $response->assertSessionHasErrors(['certificate_id']);
    }

    public function test_document_store_requires_file(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('documents.store'), [
            'document_name' => 'Test',
            'type'          => 'cv',
        ]);

        $response->assertSessionHasErrors(['file']);
    }

    public function test_disallowed_file_type_is_rejected(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('malware.php', 10, 'application/x-php');

        $response = $this->actingAs($user)->post(route('documents.store'), [
            'document_name' => 'Bad File',
            'type'          => 'cv',
            'file'          => $file,
        ]);

        $response->assertSessionHasErrors(['file']);
        $this->assertCount(0, Storage::disk('local')->allFiles());
    }

    public function test_guest_cannot_upload_document(): void
    {
        Storage::fake('local');

        $response = $this->post(route('documents.store'), [
            'document_name' => 'Test',
            'type'          => 'cv',
            'file'          => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('login'));
    }

    // ─── Download ────────────────────────────────────────────────────────────

    public function test_user_can_download_own_document(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');
        $path = $file->storeAs("documents/{$user->id}", 'resume.pdf', 'local');

        $document = Document::factory()->create([
            'user_id'       => $user->id,
            'path'          => $path,
            'original_name' => 'resume.pdf',
            'mime_type'     => 'application/pdf',
            'type'          => 'cv',
        ]);

        $response = $this->actingAs($user)->get(route('documents.download', $document));

        $response->assertOk();
    }

    public function test_user_cannot_download_another_users_document(): void
    {
        Storage::fake('local');

        $user  = User::factory()->create();
        $other = User::factory()->create();

        $document = Document::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($user)->get(route('documents.download', $document));

        $response->assertForbidden();
    }

    // ─── Destroy ─────────────────────────────────────────────────────────────

    public function test_user_can_delete_own_document(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf');
        $path = $file->storeAs("documents/{$user->id}", 'cv.pdf', 'local');

        $document = Document::factory()->create([
            'user_id' => $user->id,
            'path'    => $path,
            'type'    => 'cv',
        ]);

        $response = $this->actingAs($user)->delete(route('documents.destroy', $document));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
    }

    public function test_user_cannot_delete_another_users_document(): void
    {
        Storage::fake('local');

        $user  = User::factory()->create();
        $other = User::factory()->create();

        $document = Document::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($user)->delete(route('documents.destroy', $document));

        $response->assertForbidden();
        $this->assertDatabaseHas('documents', ['id' => $document->id]);
    }
}
