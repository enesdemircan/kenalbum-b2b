<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSliderRequest;
use App\Http\Requests\UpdateSliderRequest;
use Illuminate\Http\Request;
use App\Models\Slider;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::orderBy('order')->paginate(15);
        return view('admin.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

    public function store(StoreSliderRequest $request)
    {

        $imagePath = $request->file('image')->store('sliders', 'public');

        Slider::create([
            'image' => $imagePath,
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.sliders.index')->with('success', 'Slider başarıyla oluşturuldu!');
    }

    public function edit($id)
    {
        $slider = Slider::findOrFail($id);
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(UpdateSliderRequest $request, $id)
    {

        $slider = Slider::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                Storage::disk('public')->delete($slider->image);
            }
            
            $imagePath = $request->file('image')->store('sliders', 'public');
            $slider->image = $imagePath;
        }

        $slider->fill($request->except('image'));
        $slider->is_active = $request->has('is_active');
        $slider->save();

        return redirect()->route('admin.sliders.index')->with('success', 'Slider başarıyla güncellendi!');
    }

    public function destroy($id)
    {
        $slider = Slider::findOrFail($id);
        
        if ($slider->image && Storage::disk('public')->exists($slider->image)) {
            Storage::disk('public')->delete($slider->image);
        }
        
        $slider->delete();

        return redirect()->route('admin.sliders.index')->with('success', 'Slider başarıyla silindi!');
    }
} 