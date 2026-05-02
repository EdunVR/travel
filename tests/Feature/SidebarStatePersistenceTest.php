<?php

namespace Tests\Feature;

use Tests\TestCase;

class SidebarStatePersistenceTest extends TestCase
{
    /**
     * Test that sidebar component file contains state management attributes
     */
    public function test_sidebar_component_contains_state_management_attributes(): void
    {
        $sidebarPath = resource_path('views/components/sidebar.blade.php');
        
        $this->assertFileExists($sidebarPath);
        
        $content = file_get_contents($sidebarPath);
        
        // Check that sidebar has x-data="sidebarState" attribute
        $this->assertStringContainsString('x-data="sidebarState"', $content);
        
        // Check that menu items have data-menu-parent attributes
        $this->assertStringContainsString('data-menu-parent=', $content);
        
        // Check that toggleMenu is called
        $this->assertStringContainsString('toggleMenu(', $content);
        
        // Check that isExpanded is used
        $this->assertStringContainsString('isExpanded(', $content);
    }

    /**
     * Test that sidebar component includes state management script
     */
    public function test_sidebar_includes_state_management_script(): void
    {
        $sidebarPath = resource_path('views/components/sidebar.blade.php');
        
        $this->assertFileExists($sidebarPath);
        
        $content = file_get_contents($sidebarPath);
        
        // Check for Alpine.data('sidebarState') registration
        $this->assertStringContainsString("Alpine.data('sidebarState'", $content);
        
        // Check for key methods
        $this->assertStringContainsString('expandedMenus', $content);
        $this->assertStringContainsString('toggleMenu', $content);
        $this->assertStringContainsString('isExpanded', $content);
        $this->assertStringContainsString('expandActiveMenu', $content);
        $this->assertStringContainsString('localStorage', $content);
        $this->assertStringContainsString('sidebar_expanded_menus', $content);
    }

    /**
     * Test that sidebar state management has all required methods
     */
    public function test_sidebar_state_has_required_methods(): void
    {
        $sidebarPath = resource_path('views/components/sidebar.blade.php');
        
        $content = file_get_contents($sidebarPath);
        
        // Check for init method
        $this->assertStringContainsString('init()', $content);
        
        // Check for expandActiveMenu method
        $this->assertStringContainsString('expandActiveMenu()', $content);
        
        // Check for toggleMenu method with parameter
        $this->assertStringContainsString('toggleMenu(menuId)', $content);
        
        // Check for isExpanded method with parameter
        $this->assertStringContainsString('isExpanded(menuId)', $content);
        
        // Check for saveState method
        $this->assertStringContainsString('saveState()', $content);
    }

    /**
     * Test that sidebar state management loads from localStorage
     */
    public function test_sidebar_loads_state_from_localstorage(): void
    {
        $sidebarPath = resource_path('views/components/sidebar.blade.php');
        
        $content = file_get_contents($sidebarPath);
        
        // Check that it loads from localStorage
        $this->assertStringContainsString("localStorage.getItem('sidebar_expanded_menus')", $content);
        
        // Check that it saves to localStorage
        $this->assertStringContainsString("localStorage.setItem('sidebar_expanded_menus'", $content);
        
        // Check that it parses JSON
        $this->assertStringContainsString('JSON.parse', $content);
        $this->assertStringContainsString('JSON.stringify', $content);
    }

    /**
     * Test that sidebar auto-expands based on current route
     */
    public function test_sidebar_auto_expands_based_on_route(): void
    {
        $sidebarPath = resource_path('views/components/sidebar.blade.php');
        
        $content = file_get_contents($sidebarPath);
        
        // Check that it gets current path
        $this->assertStringContainsString('window.location.pathname', $content);
        
        // Check that it queries for active menu item
        $this->assertStringContainsString('querySelector', $content);
        
        // Check that it finds parent menu
        $this->assertStringContainsString('closest', $content);
        $this->assertStringContainsString('[data-menu-parent]', $content);
    }
}
