<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('item') || !Schema::hasTable('menu')) {
            return;
        }

        $hasType = Schema::hasColumn('item', 'type');
        if (!$hasType) {
            return;
        }

        $menuItems = DB::table('item')->where('type', 'menu')->get();
        if ($menuItems->isEmpty()) {
            return;
        }

        foreach ($menuItems as $menuItem) {
            DB::table('menu')->updateOrInsert(
                ['id' => $menuItem->id],
                [
                    'sppg_id' => $menuItem->sppg_id,
                    'nama' => $menuItem->nama,
                    'kategori_id' => $menuItem->kategori_id,
                    'uom_id' => $menuItem->uom_id,
                    'created_at' => $menuItem->created_at,
                    'updated_at' => $menuItem->updated_at,
                    'deleted_at' => $menuItem->deleted_at,
                ]
            );
        }

        if (Schema::hasTable('item_bom') && Schema::hasTable('menu_item')) {
            $menuIds = $menuItems->pluck('id')->all();
            $oldBom = DB::table('item_bom')->whereIn('item_id', $menuIds)->get();

            foreach ($oldBom as $row) {
                DB::table('menu_item')->updateOrInsert(
                    [
                        'menu_id' => $row->item_id,
                        'item_id' => $row->component_item_id,
                    ],
                    [
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ]
                );
            }
        }

        DB::table('item')->where('type', 'menu')->delete();
    }

    public function down(): void
    {
        if (!Schema::hasTable('item') || !Schema::hasTable('menu')) {
            return;
        }

        if (!Schema::hasColumn('item', 'type')) {
            return;
        }

        $menus = DB::table('menu')->get();
        foreach ($menus as $menu) {
            DB::table('item')->updateOrInsert(
                ['id' => $menu->id],
                [
                    'sppg_id' => $menu->sppg_id,
                    'nama' => $menu->nama,
                    'type' => 'menu',
                    'kategori_id' => $menu->kategori_id,
                    'uom_id' => $menu->uom_id,
                    'created_at' => $menu->created_at,
                    'updated_at' => $menu->updated_at,
                    'deleted_at' => $menu->deleted_at,
                ]
            );
        }

        if (Schema::hasTable('item_bom') && Schema::hasTable('menu_item')) {
            $pivot = DB::table('menu_item')->get();
            foreach ($pivot as $row) {
                DB::table('item_bom')->updateOrInsert(
                    [
                        'item_id' => $row->menu_id,
                        'component_item_id' => $row->item_id,
                    ],
                    [
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ]
                );
            }
        }

        DB::table('menu')->delete();
    }
};
