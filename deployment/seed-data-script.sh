#!/bin/bash

# Seed Data Script for Hajj and Umrah Travel Management System
# This script seeds initial data required for the system to function

echo "=========================================="
echo "Seeding Initial Data"
echo "=========================================="
echo ""

# Check if we're in the correct directory
if [ ! -f "artisan" ]; then
    echo "Error: artisan file not found. Please run this script from the project root."
    exit 1
fi

# Seed Workflow Stages
echo "1. Seeding Workflow Stages..."
php artisan db:seed --class=WorkflowStageSeeder
if [ $? -eq 0 ]; then
    echo "✓ Workflow stages seeded successfully"
else
    echo "✗ Failed to seed workflow stages"
    exit 1
fi
echo ""

# Seed Teams
echo "2. Seeding Teams..."
php artisan db:seed --class=TeamSeeder
if [ $? -eq 0 ]; then
    echo "✓ Teams seeded successfully"
else
    echo "✗ Failed to seed teams"
    exit 1
fi
echo ""

# Seed Permissions
echo "3. Seeding Travel Permissions..."
php artisan db:seed --class=TravelPermissionSeeder
if [ $? -eq 0 ]; then
    echo "✓ Permissions seeded successfully"
else
    echo "✗ Failed to seed permissions"
    exit 1
fi
echo ""

# Optional: Seed UAT Test Data (only for staging/testing)
read -p "Do you want to seed UAT test data? (y/n) " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "4. Seeding UAT Test Data..."
    php artisan db:seed --class=UATDataSeeder
    if [ $? -eq 0 ]; then
        echo "✓ UAT test data seeded successfully"
    else
        echo "✗ Failed to seed UAT test data"
        exit 1
    fi
    echo ""
fi

echo "=========================================="
echo "Seeding Complete!"
echo "=========================================="
echo ""
echo "Summary:"
echo "- Workflow Stages: 12 stages created"
echo "- Teams: 5 teams created"
echo "- Permissions: Travel management permissions created"
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "- UAT Test Data: Sample data created"
fi
echo ""
echo "Next steps:"
echo "1. Verify seeded data in database"
echo "2. Assign users to teams"
echo "3. Assign permissions to roles"
echo "4. Test system functionality"
