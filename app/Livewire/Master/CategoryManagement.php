<?php

namespace App\Livewire\Master;

use App\Models\Category;
use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class CategoryManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $name = '';
    public $categoryId = null;
    public $showModal = false;
    public $editMode = false;

    protected $updatesQueryString = ['search'];

    public function mount()
    {
        if (!auth()->user()->can('manage categories')) {
            abort(403, 'Unauthorized');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->name = '';
        $this->categoryId = null;
        $this->editMode = false;
        $this->resetErrorBag();
    }

    public function save()
    {
        $rules = [
            'name' => 'required|min:2|unique:categories,name,' . $this->categoryId,
        ];

        $this->validate($rules);

        if ($this->editMode) {
            $category = Category::find($this->categoryId);
            $oldData = $category->toArray();
            $category->update([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
            ]);

            ActivityLog::log([
                'action' => 'updated',
                'module' => 'categories',
                'description' => "Memperbarui kategori: {$this->name}",
                'old_values' => $oldData,
                'new_values' => $category->fresh()->toArray()
            ]);

            $this->dispatch('notify', 'Kategori berhasil diperbarui.');
        } else {
            $category = Category::create([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
            ]);

            ActivityLog::log([
                'action' => 'created',
                'module' => 'categories',
                'description' => "Menambah kategori baru: {$this->name}",
                'new_values' => $category->toArray()
            ]);

            $this->dispatch('notify', 'Kategori baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->editMode = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);
        
        // Check if category is used by products
        if ($category->products()->count() > 0) {
            $this->dispatch('notify', 'Gagal: Kategori masih digunakan oleh beberapa produk.', 'error');
            return;
        }

        $oldData = $category->toArray();
        $category->delete();

        ActivityLog::log([
            'action' => 'deleted',
            'module' => 'categories',
            'description' => "Menghapus kategori: {$oldData['name']}",
            'old_values' => $oldData
        ]);

        $this->dispatch('notify', 'Kategori berhasil dihapus.');
    }

    public function render()
    {
        $categories = Category::where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.master.category-management', [
            'categories' => $categories
        ]);
    }
}
