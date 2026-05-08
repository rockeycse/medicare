public function up(): void
{
    Schema::create('medical_records', function (Blueprint $table) {
        $table->id();
        $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
        $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
        $table->string('file_name');
        $table->string('file_path');
        $table->string('file_type');
        $table->integer('file_size');
        $table->text('description')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
}
