package com.company.assetmanagementwebservice.service;

import java.util.List;

import com.company.assetmanagementwebservice.model.dto.CategoryDTO;


public interface CategoryService {
	List<CategoryDTO> getCategoryList();
	
	CategoryDTO findCategoryById(Integer id);
	
	CategoryDTO createCategory(CategoryDTO payload);

    /**
     * Safely parses an integer from a string value.
     * @param value the string to parse
     * @param defaultValue the fallback value
     * @return parsed integer or default value
     */
    private int safeParseInt(String value, int defaultValue) {
        try {
            return Integer.parseInt(value);
        } catch (NumberFormatException e) {
            return defaultValue;
        }
    }

}
